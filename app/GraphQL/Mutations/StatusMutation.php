<?php

namespace App\GraphQL\Mutations;

use App\Repository\Session\SessionRepository;
use App\Repository\Status\StatusRepository;

final class StatusMutation
{
   protected $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public  function statusSwitch($_,$args) 
    {
        return $this->sessionRepository->openSession($args['status_id']);
    }
}
