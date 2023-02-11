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

    public function upsert($_,array $args)
    {
        return User::upsertInstance($args);
    }
}
