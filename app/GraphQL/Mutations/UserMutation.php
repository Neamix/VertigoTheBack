<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserMutation
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;   
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

    public function upsert($_,array $args)
    {
        return $this->user->upsertInstance($args);
    }

    public  function profileEdit($_,array $args)
    {
        return Auth::user()->updateProfile($args['input']);
    }
}
