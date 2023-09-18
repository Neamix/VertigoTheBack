<?php 

namespace App\Http\Services;

use App\Models\Session;
use App\Repository\Session\SessionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PusherService  {
    protected $request;
    protected $sessionRepository;

    public function __construct(Request $request,SessionRepository $sessionRepository)
    {
        $this->request = $request;
        $this->sessionRepository = $sessionRepository;
    }

    public function sessions() 
    {
        $notification = $this->request;

        // Terminate Session
        if ( $notification['events'][0]['name'] == 'member_removed') {
            $this->sessionRepository->closeSession();
        }
    }
}