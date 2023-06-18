<?php

namespace App\GraphQL\Mutations;

use App\Models\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserMutation
{
    protected $user;
    protected $request;

    public function __construct(User $user,Request $request)
    {
        $this->user = $user;  
        $this->request = $request; 
    }

    // Authentication Function
    public function loginUser($_, array $args)
    {
        return $this->user->login($args['input']['email'],$args['input']['password']);
    }

    public function forgetPassword($_,array $args)
    {
        return $this->user->forgetPassword($args['input']['email']);
    }

    public function resetPassword($_,array $args)
    {
        return $this->user->resetPassword($args['input']['email'],$args['input']['otp'],$args['input']['verificationID'],$args['input']['password']);
    }

    public function inviteMember($_,array $args)
    {
        return $this->user->inviteRequest($args['input']);
    }

    public  function profileEdit($_,array $args)
    {
        return Auth::user()->updateProfile($args['input']);
    }

    public function changEmail($_,array $args)
    {
        return $this->request->changEmail($args['email']);
    }

    // Status Functions
    public  function changeStatus($_,array $args)
    {
        return Auth::user()->changeStatus($args);
    }

    // Accept Invitation 
    public function acceptInvitation($_,array $args)
    {
        return $this->user->acceptInvitation($args['input']);
    }
}
