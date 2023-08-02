<?php

namespace App\GraphQL\Queries;

use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class JoinRequestQuery
{
    protected $joinRequest;

    public function __construct(JoinRequest $joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    /*** List Pending Requests */
    public function pendingRequests()
    {
        return $this->joinRequest->getPendingRequests();
    }

    /*** Delete Pending Requests */
    public function deleteRequest($_,array $args)
    {
        return $this->joinRequest->deletePendingRequest($args);
    }

}