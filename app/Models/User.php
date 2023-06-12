<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\UserStatusEvent;
use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,MailerService;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*** Authunticate new user */
    public function login(string $email,string $password)
    {       
        // Get Relevent User
        $user = User::where('email',$email)->first();
        $status = (password_verify($password,$user->password)) ? "Success" : "Fail";

        // Check if credentions is wrong
        if ( $status != "Success") {
            return [
                'status' => "Fail",
                'user' => null
            ];
        }

        // Make Firt Company Is The Active Company In Case User Doesn't Have One
        if ( ! $user->active_company_id ) {
            $user->active_company_id = $user->companies->first()->id;
            $user->save();
        }

        // Return Success Response
        return [
            'status' => "Success",
            'token' => $user->createToken('login')->accessToken,
            'user' => $user
        ];
    }

    /*** Send forget email **/
    public  function forgetPassword(string $email)
    {
        // Get Relevent User
        $user = User::where('email',$email)->first();
        $otp = Otp::generateOtp($user,'password_reset');

        // Send Forget Email
        $this->forgetPasswordMail([
            'name'  => $user->name,
            'to_email' => $user->email,
            'otp'   => $otp['otp']
        ]);
        
        return $otp['verification_id'];
    }

    /*** Check Otp and send status */
    static function checkOtp(string $otp,string $email) 
    {
        // Get Relevent User & otp
        $user = User::where('email',$email)->first();
        $otp  = $user->otp->where('otp',$otp)->first();

        // Send Status
        if ( $otp ) {
            return ['status' => 'Success'];
        } else {
            return ['status' => 'Failed'];
        }
    }

    /*** Add New Password */
    public function resetPassword(string $email,string $otp,string $verification_id,string $password)
    {
        $user = User::where('email',$email)->first();

        // Get Otp
        $userOtp = Otp::where([
            'user_id' => $user->id,
            'otp'   => $otp,
            'type'  => 'password_reset'
        ])->first();

        // Check Otp & verification id is right or not
        if ( password_verify($verification_id,$userOtp->verification_id) ) {
            $user->password = Hash::make($password);
            $user->save();
            return [
                'status' => "Success",
                'token'  => $this->login($email,$password)['token']
            ];
        } else {
            return [
                'status' => "Fail"
            ];
        }
    }

    /** Update User Profile */
    public function updateProfile(array $request)
    {
        $user = Auth::user();

        if ( isset($request['name']) ) {
            $user->name = $request['name'];
            $user->save();
        }

        elseif ( isset($request['phone']) ) {
            $user->phone = $request['phone'];
            $user->save();
        }

        return Auth::user();
    }

    /**
     * @deprecated Feature Has Been Waved 
    */
    public function confirmEmailRequest($request)
    {
        Auth::user()->email = $request['email'];
        Auth::user()->save();
    }

    /*** Change User Status  */
    public function changeStatus($request) 
    {
        // Change Status ID 
        $this->status_id = $request['status_id'];
        $this->save();

        // Send Pusher Event
        event(new UserStatusEvent([
            'user_id' => Auth::user()->id,
            'name'    => Auth::user()->name,
        ]));
        return $this;
    }

    /*** Render Invitation New Member */
    public static function renderInvitation($data)
    {
        $user = self::where('email',$data['email'])->first();

        if ( ! $user ) {
            $user = self::create([
                'email' => $data['email']
            ]);
        }

        if ( $user->password ) {
            JoinUsEmail::where('email',)
        }

        return [
            'type' => $user->password  ? 'existuser' : 'newuser'
        ];
    }

    /*** Accept Invitation */
    public function acceptInvitation($data)
    {
        $joinRequest = JoinUsEmail::where('email',$data['email'])->first();

        // Attach User To The New Company
        $user = self::where('email',$data['email'])->first();
        $user->companies()->attach($joinRequest['company_id']);

        // Terminate Join Request
        $joinRequest->delete();

        // In Case No Password Then This User Is New
        if ( ! $user->password ) {
            $user->password = $data['password'] ?? '';
            $user->save();
        }

        return [
            'status' => 'Success',
            'message' => 'You have join new workspace'
        ];
    }

    /*** Create InvitationRequest */
    public function inviteRequest($data) 
    {
        // Generate Token
        $token = rand(10000,99999999);

        // Save Join Request
        JoinRequest::create([
            'email' => $data['email'],
            'token' => bcrypt($token),
            'company_id' => Auth::user()->active_company_id,
        ]);

        // Send Invitation Request
        $this->joinUsMail([
            'to_email' => $data['email'],
            'company'  => Company::find(Auth::user()->active_company_id)->name,
            'token'    => url("/accept/invitation?token=$token&email=".$data['email'])
        ]);

        return [
            'email' => $data['email']
        ];
    }

    static function createInstance(array $request)
    {
        $user = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type'  => $request['type'] ?? 1,
            'phone' => $request['phone'] ?? null
        ]);

        return $user;
    }


    static function generateRootUser(array $request,$company_id)
    {
        $rootUser = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type'  => 2,
            'phone' => $request['phone'] ?? null,
            'active_company_id' => $company_id
        ]);
        
        $rootUser->companies()->attach($company_id);

        return $rootUser;
    }

    // Scopes
    public function scopeFilter($query,$request)
    {
        if ( isset($request["input"]['name']) ) {
            $query->where('name','like','%'.$request["input"]['name'].'%');
        }

        $query->where('active_company_id',Auth::user()->active_company_id);
        
        return $query;
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function activeCompany()
    {
        return $this->belongsTo(Company::class,'active_company_id');
    }
}
