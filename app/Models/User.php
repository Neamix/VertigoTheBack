<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;

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

    public function login(string $email,string $password)
    {       
        $user = User::where('email',$email)->first();
        $status = (password_verify($password,$user->password)) ? "Success" : "Fail";

        if ( $status == "Success") 
            return [
                'status' => "Success",
                'token' => $user->createToken('login')->accessToken,
                'user' => $user
            ];
        else 
            return [
                'status' => "Fail",
                'user' => null
            ];
    }

    public  function forgetPassword(string $email)
    {
        $user = User::where('email',$email)->first();
        $otp = Otp::generateOtp($user,'reset_password');

        $this->forgetPasswordMail([
            'name'  => $user->name,
            'to_email' => $user->email,
            'otp'   => $otp['otp']
        ]);
        
        return $otp['verification_id'];
    }

    public function resetPassword(string $email,string $otp,string $verification_id,string $password)
    {
        $userOtp = Otp::where([
            'email' => $email,
            'otp'   => $otp,
            'type'  => 'reset_password'
        ])->first();

        if ( password_verify($verification_id,$userOtp->verification_id) ) {
            $user = User::where('email',$email)->first();
            $user->password = Hash::make($password);
            return [
                'status' => "Success",
                'user' => $user
            ];
        } else {
            return [
                'status' => "Fail"
            ];
        }
    }

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

    public function confirmEmailRequest($request)
    {
        Auth::user()->email = $request['email'];
        Auth::user()->save();
    }

    // Crud Function 
    public static function inviteMember($email,$company_id)
    {
        JoinRequest::createRequest([
            'email' => $email,
            'company_id' => $company_id
        ]);
        
        return [
            'status' => "Success"
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
            'phone' => $request['phone'] ?? null
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
}
