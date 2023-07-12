<?php

namespace App\GraphQL\Queries;

use App\Models\Session;

final class SessionQuery
{
    public $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getSessions($_,array $args)
    {
        return $this->session->getAllSessions();
    }
}
