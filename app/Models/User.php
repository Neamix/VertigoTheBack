<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\Role;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  Access To User Account 
     * 
     *  @param string email
     *  @param string password
     * 
     *  @return array
    */

    public static function login(string $email,string $password)
    {        
        $user = User::where('email',$email)->first();
        $status = (password_verify($password,$user->password)) ? "Success" : "Fail";

        if ( $status == "Success") 
            return [
                'status' => "Success",
                'token' => $user->createToken('login',['regular'])->accessToken->token,
                'user' => $user
            ];
        else 
            return [
                'status' => "Fail",
                'user' => null
            ];
    }

    /**
     * Send Reset Password Mail User Password
     * 
     * @param string email
     * 
     * @return string
    */

    public  function forgetPassword(string $email)
    {
        //Get Relevant User
        $user = User::where('email',$email)->first();

        //Assign otp
        $otp = Otp::assignOtpToEmail($email);

      
        //Send email
        $this->forgetPasswordMail([
            'name'  => $user->name,
            'to_email' => $user->email,
            'otp'   => $otp['otp']
        ]);
        
        return $otp['verification_id'];
    }

    /**
     * Change User Password
     * 
     * @param string $email
     * @param string $otp
     * @param string $verification_id
     * @param string $password
     * 
     * @return array
     */

    public function resetPassword(string $email,string $otp,string $verification_id,string $password)
    {
        // Get Relevant User With Otp
        $userOtp = Otp::where([
            'email' => $email,
            'otp'   => $otp,
        ])->first();

        // Check that verification id is correct
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


    // Crud Function 
    public static function upsertInstance($request)
    {
        return User::create([
            'name'  => $request['input']['name'],
            'email' => $request['input']['email'],
            'role_id' => 1
        ]);
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
}
