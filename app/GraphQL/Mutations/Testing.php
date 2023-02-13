<?php

namespace App\GraphQL\Mutations;

final class Testing
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return "Hello";
    }
}
