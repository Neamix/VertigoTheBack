<?php 

namespace App\Repository\User;

use App\Models\Otp;
use App\Models\User;
use App\Repository\Session\SessionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;

class UserAuthRepository extends BaseRepository {

    protected $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

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

    public function login($login) : array
    {
        // Get user by his email
        $user = User::where('email',$login['email'])->first();

        // Check credintions 
        if ( ! password_verify($login['password'],$user->password) )
            return ['status' => 'fail','message' => 'Failed to authunticate'];
        
        // Set active company in case no active companu for that user
        if ( ! $user->active_company_id ) {
            $user->active_company_id = $user->companies->first()->id;
            $user->save();
        }

        // Return autuntication token 
        return ['status' => 'success','user' => $user,'token' => $user->createToken('login')->accessToken];
    }

    /**
     * Forget password 
     * @param email string
     * @return verification_id
    */

    public function forgetPassword(string $email) : string
    {
        // Get Relevent User
        $user = Otp::where('email',$email)->first();
        $otp = Otp::generateOtp($user,'password_reset');
 
        // Send Forget Email
        $this->forgetPasswordMail(['name'  => $user->name,'to_email' => $user->email,'otp'   => $otp['otp']]);
         
        return $otp['verification_id'];
    }

    /**
     * Check otp
     * @param otp 
     * @param email 
     * @return array
    */

    public function checkOtp(string $otp,string $email) 
    {
        // Get Relevent User & otp
        $user = User::where('email',$email)->first();
        $otp  = $user->otp->where('otp',$otp)->first();

        // Send Status
        return ($otp) ? ['status' => 'Success'] : ['status' => 'Failed'];
    }

    /**
     * Reset password
     * @param user user reset data
     * @return array
    */

    public function resetPassword($email,$userOtp,$verficationID,$password) : array
    {
        // Get user by email
        $user = Otp::where('email',$email)->first();

        // Get otp
        $otp = Otp::where(['user_id' => $user->id,'otp' => $userOtp,'type'  => 'password_reset'])->first();

        // Check otp
        if ( ! password_verify($verficationID,$otp->verification_id) ) 
            return ['status' => "Fail"];

        // Change user password
        $user->password = Hash::make($password);
        $user->save();

        // Remove otp
        $otp->delete();

        // Return Response
        return ['status' => "Success",'token'  => $this->login(['email' => $email,'password' => $password])['token']];
    }

    /**
     * Logout Member
     * @return array
     */

    public function logout() : array
    {
        $this->sessionRepository->closeSession();
        Auth::user()->token()->revoke();
        return ['status' => 'success'];
    }

}