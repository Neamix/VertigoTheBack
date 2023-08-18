<?php 

namespace App\Repository\Status;

use App\Events\UserStatusEvent;
use App\Repository\Session\SessionRepository;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class StatusRepository extends BaseRepository{

    protected $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function model () 
    {
        return Status::class;
    }

    /**
     *  Change auth user status
     *  @param status_id int
    */

    public function changeStatus($status_id) 
    {
        // Change status id
        Auth::user()->status_id = $status_id;
        Auth::user()->save();

        // Close sessions if exists and open new one
        $this->sessionRepository->closeSession();
        $session = $this->sessionRepository->openSession();

        // Send status notification
        event(new UserStatusEvent([
            'user_id' => Auth::user()->id,
            'status_id'    => $status_id,
        ]));

        // Return response
        return [
            'statusid' => $status_id,
            'session'  => $session 
        ];
    }

}