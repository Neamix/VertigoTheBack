<?php 

namespace App\Repository\User;

use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;

class UserAuthRepository extends BaseRepository {

    public function model()
    {
        return User::class;
    }

    /**
     * Member authunticate
     * @param user array
     * @return array
     * 
    */

    public function login($user) : array
    {
        // Get user by his email
        $user = $this->where('email',$user['email'])->first();

        // Check credintions 
        if ( ! password_verify($user['password'],$user->password) )
            return ['status' => 'fail','message' => 'Failed to authunticate'];
        
        // Set active company in case no active companu for that user
        if ( ! $user->active_company_id ) {
            $user->active_company_id = $user->companies->first()->id;
            $user->save();
        }

        // Return autuntication token 
        return ['status' => 'success','user' => $user,'token' => $user->createToken()->accessToken];

    }

    /**
     * Forget password 
     * @param email string
     * @return verification_id
    */

    public function forgetPassword(string $email) : string
    {
        // Get Relevent User
        $user = $this->where('email',$email)->first();
        $otp = Otp::generateOtp($user,'password_reset');
 
        // Send Forget Email
        $this->forgetPasswordMail(['name'  => $user->name,'to_email' => $user->email,'otp'   => $otp['otp']]);
         
        return $otp['verification_id'];
    }

    /**
     * Reset password
     * @param user user reset data
     * @return array
    */

    public function resetPassword($user) : array
    {
        // Get user by email
        $user = $this->where('email',$user['email'])->first();

        // Get otp
        $otp = Otp::where(['user_id' => $user->id,'otp' => $user['otp'],'type'  => 'password_reset'])->first();

        // Check otp
        if ( ! password_verify($user['verification_id'],$otp->verification_id) ) 
            return ['status' => "Fail"];

        // Change user password
        $user->password = Hash::make($user['password']);
        $user->save();

        // Return Response
        return ['status' => "Success",'token'  => $this->login($user['email'],$user['password'])['token']];
    }

    /**
     * Logout Member
     * @return array
     */

    public function logout() : array
    {
        Auth::user()->token()->revoke();
        return ['status' => 'success'];
    }

}