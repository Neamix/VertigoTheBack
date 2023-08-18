<?php

namespace App\GraphQL\Mutations;
use App\Repository\Session\SessionRepository;

final class SessionMutation
{
    protected $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;    
    }

    /** Open new session for auth user */
    public function openSession($_, array $args)
    {
        return $this->sessionRepository->openSession();
    }

    /** Close session for auth user */
    public function closeSession($_, array $args)
    {
        return $this->sessionRepository->closeSession();
    }
}
