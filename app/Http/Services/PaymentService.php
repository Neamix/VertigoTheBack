<?php 

namespace App\Http\Services;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentService  {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequestType() {
        Log::info($this->request['type']);
    }

    /**
     * Get the workspace with stripe id revent to customer send by stripe 
     * 
     * @return Workspace
    */
    public function getReleventWorkspace() : Workspace
    {
        $stripe_id = $this->request['data']['object']['customer'];
        return Workspace::where('stripe_id',$stripe_id)->first();
    }

    public function getCancelDate() 
    {
        return $this->request['date'];
    }

}
