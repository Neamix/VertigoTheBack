<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\SessionEvent;
use App\Events\UserStatusEvent;
use App\Exports\UserMonitoringSheet;
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
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
    static function login(string $email,string $password)
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

        // Make Firt Workspace Is The Active Workspace In Case User Doesn't Have One
        if ( ! $user->active_workspace_id ) {
            $user->active_workspace_id = $user->companies->first()->id;
            $user->save();
        }

        // Return Success Response
        return ['status' => "Success",'token' => $user->createToken('login')->accessToken,'user' => $user];
    }

    /*** User Logout */
    public function logout()
    {
        Auth::user()->token()->revoke();
        // return [
        //     'Status' => 'Success',
        //     ''
        // ]
    }

    /*** Send forget email **/
    public  function forgetPassword(string $email)
    {
        // Get Relevent User
        $user = User::where('email',$email)->first();
        $otp = Otp::generateOtp($user,'password_reset');

        // Send Forget Email
        $this->forgetPasswordMail(['name'  => $user->name,'to_email' => $user->email,'otp'   => $otp['otp']]);
        
        return $otp['verification_id'];
    }

    /*** Check Otp and send status */
    static function checkOtp(string $otp,string $email) 
    {
        // Get Relevent User & otp
        $user = User::where('email',$email)->first();
        $otp  = $user->otp->where('otp',$otp)->first();

        // Send Status
        return ($otp) ? ['status' => 'Success'] : ['status' => 'Failed'];
    }

    /*** Add New Password */
    public function resetPassword(string $email,string $otp,string $verification_id,string $password)
    {
        $user = User::where('email',$email)->first();

        // Get Otp Releated To User
        $userOtp = Otp::where(['user_id' => $user->id,'otp' => $otp,'type'  => 'password_reset'])->first();

        if ( password_verify($verification_id,$userOtp->verification_id) ) {
            // Hash user password
            $user->password = Hash::make($password);
            $user->save();

            // Return Response
            return ['status' => "Success",'token'  => $this->login($email,$password)['token']];
        } else {
            return ['status' => "Fail"];
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

        // Close Current Session
        Auth::user()->terminateSession(Auth::user()->active_workspace_id);
        
        // Open New Session
        Auth::user()->openSession(Auth::user()->active_workspace_id);

        // Send Pusher Event
        event(new UserStatusEvent([
            'user_id' => $this->id,
            'status_id'    => $this->status_id,
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
            $user->acceptInvitation($data);
        }

        return [
            'type' => $user->password  ? 'existuser' : 'newuser'
        ];
    }

    /*** Accept Invitation */
    public function acceptInvitation($data)
    {
        $joinRequest = JoinRequest::where('email',$data['email'])->first();

        // Get User Under Action
        $user = self::where('email',$data['email'])->first();

        // Attach User To The New Workspace
        $user->companies()->attach($joinRequest['workspace_id']);

        // Get The Requested Workspace
        $workspace = $joinRequest->workspace()->first(['name','id']);
        
        // Terminate Join Request
        $joinRequest->delete();

        // In Case No Password Then This User Is New
        if ( ! $user->password ) {
            $user->password = Hash::make($data['password']);
            $user->name = $data['name'] ?? "Vertigo User";
            $user->status_id = 1;
        }

        // Change User Active Workspace To The New Workspace
        $user->active_workspace_id = $workspace->id;
        $user->save();

        // Authunticate User
        if ( isset($data["password"]) ) {
            return  $this->login($user->email,$data["password"]);
        }
    }

    /*** Create InvitationRequest */
    public function inviteRequest($data) 
    {
        // Generate Token
        $token = rand(10000,99999999);

        // Save Join Request
        $request = JoinRequest::updateOrCreate([
            'email' => $data['email']
        ],
        [
            'email' => $data['email'],
            'token' => bcrypt($token),
            'workspace_id' => Auth::user()->active_workspace_id,
        ]);

        // Send Invitation Request
        $this->joinUsMail([
            'to_email' => $data['email'],
            'workspace'  => Workspace::find(Auth::user()->active_workspace_id)->name,
            'token'    => url("/accept/invitation?token=$token&email=".$data['email'])
        ]);

        return [
            'email' => $request->email,
            'id'    => $request->id
        ];
    }

    /*** Create Instance */
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

    /*** Switch Active Workspace */
    public function switchWorkspace(array $request)
    {
        // Switch Workspace In DB
        $this->active_workspace_id = $request['workspaceid'];
        $this->save();

        return [
            'status' => 'success',
            'user'   => Auth::user()
        ];
    }

    /*** Generate Root User */
    static function generateRootUser(array $request,$workspace_id)
    {
        // Create Root User
        $rootUser = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type'  => 2,
            'phone' => $request['phone'] ?? null,
            'active_workspace_id' => $workspace_id
        ]);
        
        // Attach Root User To The Created Workspace
        $rootUser->companies()->attach($workspace_id);

        return $rootUser;
    }

    /*** Toggle Suspend User */
    public function toggleUserSuspended($user_id) 
    {
        // Get User Under Action
        $user = User::where([
            'id' => $user_id
        ])->first();
        
        // Get User Status
        $is_suspended = $user->companies()->where('workspace_id',$this->active_workspace_id)->first()->pivot->is_suspend;

        // Reverse Status
        $user->companies()->updateExistingPivot($this->active_workspace_id,[
           'is_suspend' => ! $is_suspended
        ]);
    
        return [
            'status'  => "Success",
        ];
    }

    /*** Delete User */
    public function deleteUser($user_id) 
    {   
        // Get User Under Action
        $user = User::where([
            'id' => $user_id
        ])->first();
        
        // Terminate User
        $user->delete();
    }

    /*** Check If User Suspended Or Not */
    public function isSuspended() 
    {
        return $this->companies()->where('workspace_id',$this->active_workspace_id)->first()->pivot->is_suspend;
    }

    /*** Terminate  Session */
    public function terminateSession($workspace_id = null,$timestamp = null)
    {
        // Set User To Inactive 
        $this->companies()->where('workspace_id',$workspace_id)->update([
            'is_active' => false
        ]);

        // Set termination date
        $end_date =  $timestamp ?? now();
        
        // Set workspace id (note: not recommended to not send workspace id in case u used it on third party request)
        $workspace_id = $workspace_id ?? $this->active_workspace_id;

        // Session Select
        $session = $this->session()->where('workspace_id',$workspace_id)->latest()->first();

        // Terminate session if it exist
        if ( $session ) {
            $session->end_date = $end_date;
            $session->total_session_time = strtotime($end_date) - strtotime($session->start_date);
            $session->save();

            // Send Notify To Root Account
            event(new SessionEvent([
                'status_id' => $session->status_id,
                'total_session_time' => $session->total_session_time,
                'workspace_id' => $session->workspace_id
            ])); 
        }        
    }

    /*** openSession */
    public function openSession($workspace_id,$timestamp = null)
    {
        // Set User To Active
        $this->companies()->where('workspace_id',$workspace_id)->update([
            'is_active' => true
        ]);
        
        // Create Session
        $this->session()->create([
            'workspace_id' => $workspace_id,
            'status_id'  => $this->status_id,
            'start_date' => $timestamp ?? now()
        ]);
    }

    /*** Export Members Monitoring Sheet */
    public function exportMonitoringSheet($export)
    {
        // Set Start Date 
        if ( $export['input']['duration'] == 1 ) {
            $start_date = date('m-01-Y');
        } else if ( $export['input']['duration'] == 2 ) {
            $start_date = date('Y-m-01', strtotime(now(). ' - 2 months'));
        } else {
            $start_date = date('Y-m-01', strtotime(now(). ' - 3 months'));
        }

        $end_date  = date('Y-m-d');

        // Filter Users
        $startID = 40 * ($export['input']['filters']['page'] - 1);
        $endID   = $startID + 40;

        $users = User::filter($export['input']['filters'])->with(['session' => function ($query) use ($start_date,$end_date) {
            $query->whereBetween('created_at',[$start_date,$end_date]);
        }])->whereBetween('id',[$startID,$endID])->get();

        // Set File Name
        $filename = 'exporting/'.Auth::user()->active_workspace_id.'/'.rand(100000,9000000).'.xlsx';

        // Generate Excel Sheet
        Excel::store(new UserMonitoringSheet($users),$filename,'main');

        return [
            'path' => env('APP_URL').'/'. $filename
        ];
    }

    // Scopes
    public function scopeFilter($query,$request)
    {
        if ( isset($request["input"]['name']) ) {
            $query->where('name','like','%'.$request["input"]['name'].'%');
        }
        
        $query->whereHas('companies',function ($subQuery) {
            $subQuery->where('workspace_id',Auth::user()->active_workspace_id);
        });
        
        return $query;
    }

    // Attributes

    /*** Get Active Hours */
    public function getActiveHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',ACTIVE)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Idle Hours */
    public function getIdleHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',IDLE)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Meeting Idle Hours */
    public function getMeetingHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',MEETING)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Total Hours */
    public function getTotalHoursAttribute()
    {
        $tracked_time = $this->session->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Companies User Allowed To Access */
    public function getAccessableCompaniesAttribute()
    {
        return $this->companies()->wherePivot('is_suspend',false)->get();
    }

    /*** Get If Current User Is Root Account On Current Workshop */
    public function getIsRootAttribute()
    {
        return Workspace::where('user_id',Auth::user()->id)->count();
    }

    /*** Get If Current User Is Suspended On Current Workshop */
    public function getIsSuspendAttribute()
    {
        return $this->companies()->wherePivot('is_suspend',1)->where('companies.id',Auth::user()->active_workspace_id)->count();
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Workspace::class)->withPivot(['is_suspend']);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function activeWorkspace()
    {
        return $this->belongsTo(Workspace::class,'active_workspace_id');
    }

    public function session()
    {
        return $this->hasMany(Session::class);
    }
}
