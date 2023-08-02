<?php

namespace App\GraphQL\Mutations;

use App\Models\JoinRequest;

final class JoinRequestsMutation
{
    protected $joinRequest;

    public function __construct(JoinRequest $joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    public function deleteRequest($_,array $args)
    {
        return $this->joinRequest->deletePendingRequest($args);
    }

}
