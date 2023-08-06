<?php

namespace App\GraphQL\Mutations;

use App\Models\Request;
use App\Models\User;
use App\Repository\User\UserAuthRepository;
use App\Repository\User\UserInvitationRepository;
use Illuminate\Support\Facades\Auth;

final class UserMutation
{
    protected $user;
    protected $request;
    protected $userAuthRepository;
    protected $userInvitationRepository;

    public function __construct(
        UserAuthRepository $userAuthRepository,
        UserInvitationRepository $userInvitationRepository,
        User $user,
        Request $request)
    {
        $this->user = $user;  
        $this->request = $request; 
        $this->userAuthRepository = $userAuthRepository;
        $this->userInvitationRepository = $userInvitationRepository;
    }

    // Login
    public function loginUser($_, array $args)
    {
        return $this->userAuthRepository->login($args['input']);
    }

    // Logout 
    public function logout()
    {
        return $this->userAuthRepository->logout();
    }

    // Forget Password
    public function forgetPassword($_,array $args)
    {
        return $this->userAuthRepository->forgetPassword($args['input']['email']);
    }

    // Reset Password
    public function resetPassword($_,array $args)
    {
        return $this->userAuthRepository->resetPassword($args['input']['email'],$args['input']['otp'],$args['input']['verificationID'],$args['input']['password']);
    }  

    // Invite Member
    public function inviteMember($_,array $args)
    {
        return $this->userInvitationRepository->inviteMember($args['input']);
    }

    // Accept Invitation 
    public function acceptInvitation($_,array $args)
    {
        return $this->userInvitationRepository->acceptInvitation($args['input']);
    }

    // Edit Profile
    public  function profileEdit($_,array $args)
    {
        return Auth::user()->updateProfile($args['input']);
    }

    // Change Email
    public function changEmail($_,array $args)
    {
        return $this->request->changEmail($args['email']);
    }

    // Status Functions
    public  function changeStatus($_,array $args)
    {
        return Auth::user()->changeStatus($args);
    }

    // Switch Company
    public function switchCompany($_,array $args)
    {
        return Auth::user()->switchCompany($args);
    }

    // Toggle Suspended User 
    public function toggleUserSuspended($_,array $args)
    {
        return Auth::user()->toggleUserSuspended($args['user_id']);
    }

    // Delete User
    public function deleteUser($_,array $args)
    {
        return Auth::user()->deleteUser($args['user_id']);
    }

}
