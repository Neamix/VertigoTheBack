<?php 

namespace App\Http\Services;

use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PusherService  {
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function sessions() 
    {
        $notification = $this->request;
        $timestamp = date('Y-m-d H:i:s',$notification['time_ms']/1000);
        $event = $notification['events'][0];
        Log::info($this->request);
        // Start / Terminate Session
        if ( $notification['events'][0]['name'] == 'member_added') {
            Session::createSession($event,$timestamp);
        } else if ( $notification['events'][0]['name'] == 'member_removed' ) {
            Session::closeSession($event,$timestamp);
        }
    }
}