<?php

namespace App\GraphQL\Mutations;

use App\Repository\Status\StatusRepository;

final class StatusMutation
{
   protected $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public  function statusSwitch($_,$args) 
    {
        return $this->statusRepository->changeStatus($args['status_id']);
    }
}
