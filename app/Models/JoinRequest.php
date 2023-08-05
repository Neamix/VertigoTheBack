<?php

namespace App\Models;

use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class JoinRequest extends Model
{
    use HasFactory,MailerService;

    protected $guarded = [];

    public static function createRequest($request)
    {
        self::create([
            'email' => $request['email'],
            'company_id' => $request['company_id'],
        ]);
    }

    /*** Get Pending Request */
    public function getPendingRequests()
    {
        return $this->filter(['company_id' => Auth::user()->active_company_id])->get(['email','id']);
    }

    /*** Delete Pending Request */
    public function deletePendingRequest($request_id)
    {
        // Delete Join Request
        JoinRequest::where(['company_id' => Auth::user()->active_company_id,'id' => $request_id])->delete();

        return [
            'status'  => true,
            'message' => 'Join requests has been deleted'
        ];
    }

    // Scopes
    public function scopeFilter($query,$request)
    {
        if ( isset($request['company_id']) ) {
            $query->where('company_id',$request['company_id']);
        }

        return $query;
    }

    // Relation
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
