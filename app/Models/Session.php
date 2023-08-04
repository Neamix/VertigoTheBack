<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Session extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*** Open Session */
    static function createSession($data,$timestamp)
    {
        // Get Workspace ID
        $workspace_id = explode('.',$data['channel'])[1];

        // Get User In Action 
        $user = User::find($data['user_id']);

        // Open new session
        $user->openSession($workspace_id,$timestamp);
    }

    /*** Close Session */
    static function closeSession($data,$timestamp)
    {
        // Select User
        $user = User::where('id',$data['user_id'])->first();
        $workspace_id = explode('.',$data['channel'])[1];
        
        // Terminate Session
        $user->terminateSession($workspace_id,$timestamp);
    }

    /***  Generate Monitoring Sheet */
    static function generateMonitoringSheet($workspace_id,$duration_from = null,$duration_to = null,$recipient_email)
    {
        $sessions = self::where('workspace_id');
    }

    /*** Get All Sessons */
    public function getAllSessions()
    {
        $sessions = $this->where('workspace_id',Auth::user()->active_workspace_id)->whereYear('end_date',date('Y'))->get();
        return $sessions;
    }
    
    // Relation
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    // Attributes
    public function getCreatedMonthAttribute()
    {
        return date('M',strtotime($this->end_date));
    }

}
