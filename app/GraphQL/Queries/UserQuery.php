<?php

namespace App\GraphQL\Queries;

use App\Models\User;

final class UserQuery
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
    }

    public function filterUser($_,array $args)
    {
        return User::filter($args)->paginate();
    }
}
