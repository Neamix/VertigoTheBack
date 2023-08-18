<?php 

namespace App\Repository\Session;

use App\Events\UserStatusEvent;
use App\Models\Session;
use App\Repository\Status\StatusRepository;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class SessionRepository extends BaseRepository{

    public function model()
    {
        return Session::class;
    }

    /** Open new session for auth user */
    public function openSession()
    {
        // Terminate any session for auth user
        $this->closeSession();
        
        // Create new session for auth user
        $session = Auth::user()->session()->create([
            'company_id' => Auth::user()->active_company_id,  
            'status_id'  => Auth::user()->status_id ?? ACTIVE, // In case user have no status set it to active
            'start_date' => date('Y-m-d H:i:s'),
        ]);

        // Declare auth status id
        Auth::user()->status_id = $session->status_id;
        Auth::user()->save();

        // Send status notification
        event(new UserStatusEvent([
            'user_id'    => Auth::user()->id,
            'status_id'  => Auth::user()->status_id,
            'company_id' => Auth::user()->active_company_id
        ]));

        return $session;
    }

    /** Close session for auth user */
    public function closeSession()
    {
        // Close latest session for auth user
        $session = Auth::user()->session()->where('end_date',null)->where('company_id', Auth::user()->active_company_id)->orderBy('id','DESC')->first();
        
        if ( $session ) {
            $session->end_date =  date('Y-m-d H:i:s');
            $session->total_session_time = strtotime($session->end_date) - strtotime($session->start_date);
            $session->save();
        }

        // Return response
        return $session;
    }

    /*** Get All Sessons */
    public function getAllSessions()
    {
        $sessions = $this->where('company_id',Auth::user()->active_company_id)->whereYear('end_date',date('Y'))->get();
        return $sessions;
    }
}