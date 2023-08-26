<?php 

namespace App\Repository\Session;

use App\Events\SessionEvent;
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
            'user_id'      => Auth::user()->id,
            'company_id'   => Auth::user()->active_company_id,
            'status_id'    => $session->status_id,
            'session'      => [
                'id' => $session->id,
                'start_date' => $session->start_date
            ]
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

            event(new SessionEvent([
                'total_session_time' => $session->total_session_time,
                'status_id'  => $session->status_id,
                'month'      => date('M',strtotime($session['end_date'])),
                'company_id' => $session->company_id,
                'event'      => 'close_session'
            ]));
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

    /*** Get session reports */
    public function getSessionsReport()
    {
        $sessions = self::where('company_id',1)->where('end_date','!=',null)->get(['total_session_time','end_date','status_id']);

        // Get total hour achieved
        $total_hours = $sessions->groupBy(function ($session) {
            return date('M',strtotime($session->end_date));
        })->mapWithKeys(function($items,$key) {
            $achieved_time = 0;
            foreach ( $items as $item ) {
                $achieved_time += $item->total_session_time;
            }
            return [$key => $achieved_time];     
        })->all();

        // Get active hours achieved
        $active_hours = $sessions->where('status_id',ACTIVE)->groupBy(function ($session) {
            return date('M',strtotime($session->end_date));
        })->mapWithKeys(function($items,$key) {
            $achieved_time = 0;
            foreach ( $items as $item ) {
                $achieved_time += $item->total_session_time;
            }
            return [$key => $achieved_time];
        })->all();

        // Get idle hours achieved
        $idle_hours = $sessions->where('status_id',IDLE)->groupBy(function ($session) {
            return date('M',strtotime($session->end_date));
        })->mapWithKeys(function($items,$key) {
            $achieved_time = 0;
            foreach ( $items as $item ) {
                $achieved_time += $item->total_session_time;
            }
            return [$key => $achieved_time];
        })->all();

        // Get meeting hours achieved
        $meeting_hours = $sessions->where('status_id',MEETING)->groupBy(function ($session) {
            return date('M',strtotime($session->end_date));
        })->mapWithKeys(function($items,$key) {
            $achieved_time = 0;
            foreach ( $items as $item ) {
                $achieved_time += $item->total_session_time;
            }
            return [$key => $achieved_time];    
        })->all();

        return [
            'total_hours'  => $total_hours,
            'active_hours' => $active_hours,
            'idle_hours'   => $idle_hours,
            'meeting_hours' => $meeting_hours,
            'total_sessions_count' => $sessions->count()
        ];
    }
}