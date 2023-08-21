<?php 

namespace App\Repository\User;

use App\Events\MemberAddedEvent;
use App\Models\Company;
use App\Models\JoinRequest;
use App\Models\User;
use App\Services\MailerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;

class UserInvitationRepository extends BaseRepository {
    use MailerService;

    protected $userAuthRepository;

    public function __construct(UserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function model()
    {
        return User::class;
    }

    /**
     * Invite member 
     * @param member Member info 
    */

    public function inviteMember($member)
    {
        // Generate invitation token
        $token = rand(10000,99999999);

        // Create join request
        $request = JoinRequest::updateOrCreate([
            'email' => $member['email']
        ],
        [
            'email' => $member['email'],
            'token' => bcrypt($token),
            'company_id' => Auth::user()->active_company_id,
        ]);

        // Send invitation request
        $this->joinUsMail([
            'to_email' => $member['email'],
            'company'  => Company::find(Auth::user()->active_company_id)->name,
            'token'    => url("/accept/invitation?token=$token&email=".$member['email'])
        ]);

        // Send response
        return [
            'email' => $request->email,
            'id'    => $request->id
        ];
    }

    /**
     * Render Invitation New Member 
     * @param invitaion Info
    */
    public static function renderInvitation($invitation)
    {
        $user = User::firstOrCreate([
            'email' => $invitation['email']
        ],[
            'email' => $invitation['email']
        ]);

        if ( $user->password ) {
            $user->acceptInvitation($invitation);
        }

        return [
            'type' => $user->password  ? 'existuser' : 'newuser'
        ];
    }

    /** 
     * Accept invitation 
     * @param member Member info 
    */

    public function acceptInvitation($member)
    {
        // Save invitation request
        $joinRequest = JoinRequest::where('email',$member['email'])->first();

        // Create user if not exist before
        $user = User::where('email',$member['email'])->first();

        // Attach User To The New Company
        $user->companies()->attach([$joinRequest['company_id'] => ['created_at' => date('Y-m-d',strtotime('Today'))]]);
        
        // Get The Requested Company
        $company = $joinRequest->company()->first(['name','id']);
        
        // Terminate Join Request
        $joinRequest->delete();

        // In Case No Password Then This User Is New
        if ( ! $user->password ) {
            $user->password = Hash::make($member['password']);
            $user->name = $member['name'] ?? "Vertigo User";
            $user->status_id = 1;
        }

        // Change User Active Company To The New Company
        $user->active_company_id = $company->id;
        $user->save();

        // Send Pusher Notification
        event(new MemberAddedEvent([
            'user_id' => 2,
            'company_id' => 1,
            'acceptance_month' => date('M',strtotime('Today'))
        ]));
        
        // Authunticate User
        return  $this->userAuthRepository->login(['email' => $user->email,'password' => $member["password"]]);
    }

}