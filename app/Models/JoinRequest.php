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
            'workspace_id' => $request['workspace_id'],
        ]);
    }

    /*** Get Pending Request */
    public function getPendingRequests()
    {
        return $this->filter(['workspace_id' => Auth::user()->active_workspace_id])->get(['email','id']);
    }

    /*** Delete Pending Request */
    public function deletePendingRequest($request_id)
    {
        // Delete Join Request
        JoinRequest::where(['workspace_id' => Auth::user()->active_workspace_id,'id' => $request_id])->delete();

        return [
            'status'  => true,
            'message' => 'Join requests has been deleted'
        ];
    }

    // Scopes
    public function scopeFilter($query,$request)
    {
        if ( isset($request['workspace_id']) ) {
            $query->where('workspace_id',$request['workspace_id']);
        }

        return $query;
    }

    // Relation
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
