<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

        // Create Session
        self::create([
            'company_id' => $company_id,
            'user_id'    => $user->id,
            'status_id'  => $user->status_id,
            'start_date' => $timestamp ?? now()
        ]);  
    }

    /*** Close Session */
    static function closeSession($data,$timestamp)
    {
        // Select User
        $user = User::where('id',$data['user_id'])->first();
        $company_id = explode('.',$data['channel'])[1];
        $end_date =  $timestamp ?? now();

        // Session Terminate
        $session = $user->session()->where('company_id',$company_id)->latest()->first();
        $session->end_date = $end_date;
        // $session->total_session_time = strtotime($session->start_date) - $end_date;
        $session->save();
        Log::info(strtotime($session->start_date));
        Log::info(strtotime($end_date) - strtotime($session->start_date));
    }

    /***  Generate Monitoring Sheet */
    static function generateMonitoringSheet($company_id,$duration_from = null,$duration_to = null,$recipient_email)
    {
        $sessions = self::where('company_id');
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

}
