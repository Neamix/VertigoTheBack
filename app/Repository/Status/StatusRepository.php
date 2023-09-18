<?php 

namespace App\Repository\Status;

use App\Events\UserStatusEvent;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class StatusRepository extends BaseRepository{

    public function model () 
    {
        return Status::class;
    }

    /**
     *  Change auth user status
     *  @param status_id int
    */

    public function changeStatus($status_id = ACTIVE,$session) 
    {
        // Change status id
        Auth::user()->status_id = $status_id;
        Auth::user()->save();

        // Send status notification
        event(new UserStatusEvent([
            'user_id'      => Auth::user()->id,
            'company_id'   => Auth::user()->active_company_id,
            'status_id'    => $status_id,
            'session'      => [
                'id' => $session->id,
                'start_date' => $session->start_date
            ]
        ]));

        // Return response
        return [
            'statusid' => $status_id,
            'session'  => $session 
        ];
    }

}