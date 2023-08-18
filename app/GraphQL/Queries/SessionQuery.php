<?php

namespace App\GraphQL\Queries;

use App\Models\Session;
use App\Repository\Session\SessionRepository;

final class SessionQuery
{
    protected $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function getAllSessions($_,array $args)
    {
        return $this->sessionRepository->getAllSessions();
    }
}
