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
        // Get Company ID
        $company_id = explode('.',$data['channel'])[1];

        // Get User In Action 
        $user = User::find($data['user_id']);

        // Open new session
        $user->openSession($company_id,$timestamp);
    }

    /*** Close Session */
    static function closeSession($data,$timestamp)
    {
        // Select User
        $user = User::where('id',$data['user_id'])->first();
        $company_id = explode('.',$data['channel'])[1];
        
        // Terminate Session
        $user->terminateSession($company_id,$timestamp);
    }

    /***  Generate Monitoring Sheet */
    static function generateMonitoringSheet($company_id,$duration_from = null,$duration_to = null,$recipient_email)
    {
        $sessions = self::where('company_id');
    }

    /*** Get All Sessons */
    public function getAllSessions()
    {
        $sessions = $this->where('company_id',Auth::user()->active_company_id)->whereYear('end_date',date('Y'))->get();
        return $sessions;
    }
    
    // Relation
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Attributes
    public function getCreatedMonthAttribute()
    {
        return date('M',strtotime($this->end_date));
    }

}
