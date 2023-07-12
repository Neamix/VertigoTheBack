<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserQuery
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /*** Filter Users In WorkSpace */
    public function filterUser($_,array $args)
    {
        return User::filter($args)->paginate($args['first']);
    }

    /*** Check Oto */
    public function checkOtp($_,array $args) 
    {
        return User::checkOtp($args['input']['otp'],$args['input']['email']);
    }

    /*** Exporting Monitoring Sheet  */
    public function exportMonitoringSheet($_,array $args)
    {
        return  $this->user->exportMonitoringSheet($args);
    }
}
