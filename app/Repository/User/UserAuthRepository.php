<?php 

namespace App\Repository\User;

use App\Events\AuthEvent;
use App\Models\Otp;
use App\Models\User;
use App\Repository\Session\SessionRepository;
use App\Services\MailerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Eloquent\BaseRepository;

class UserAuthRepository extends BaseRepository {
    use MailerService;
    
    protected $sessionRepository;

    public function model()
    {
        return User::class;
    }

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
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

        // Logout from other devices
        $user->logoutFromAllDevices();
        
        // Terminate all seasons
        $this->sessionRepository->terminateAllSeasons($user->id);

        // Send logout event to all devices
        event(new AuthEvent([
            'user_id' => Auth::id()
        ]));
        
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
        $user = User::where('email',$email)->first();
        // Get Otp
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
        $user = User::where('email',$user['email'])->first();

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
        // Terminate tokens
        Auth::user()->logoutFromAllDevices();

        // Terminate Session
        $this->sessionRepository->closeSession();
        
        return ['status' => 'success'];
    }

}