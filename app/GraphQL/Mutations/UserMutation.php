<?php

namespace App\GraphQL\Mutations;

use App\Models\User;

final class UserMutation
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }


    // Authentication Function

    public function loginUser($_, array $args)
    {
        return (new User)->login($args['input']['email'],$args['input']['password']);
    }

    public function forgetPassword($_, array $args)
    {
        return (new User)->forgetPassword($args['input']['email']);
    }

    public function upsert($_,array $args)
    {
        return (new User)->upsertInstance($args);
    }
}
