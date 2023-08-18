<?php 

namespace App\Repository\User;

use App\Models\Company;
use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;

class UserInvitationRepository extends BaseRepository {

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
     * Accept invitation 
     * @param member Member info 
    */

    public function acceptInvitation($member)
    {
        // Save invitation request
        $joinRequest = JoinRequest::where('email',$member['email'])->first();

        // Get user by email address
        $user = self::where('email',$member['email'])->first();

        // Attach User To The New Company
        $user->companies()->attach($joinRequest['company_id']);

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

        // Authunticate User
        if ( isset($member["password"]) ) {
            return  $this->login($user->email,$member["password"]);
        }
    }

}