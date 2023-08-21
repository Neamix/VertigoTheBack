<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,MailerService;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Scopes
    public function scopeFilter($query,$request)
    {
        if ( isset($request["input"]['name']) ) {
            $query->where('name','like','%'.$request["input"]['name'].'%');
        }
        
        $query->whereHas('companies',function ($subQuery) {
            $subQuery->where('company_id',Auth::user()->active_company_id);
        });
        
        return $query;
    }

    // Attributes

    /*** Get Total Members */
    public function getTotalMembers()
    {
        return $this->withCount('companies')->get();
    }

    /*** Get Suspended Members */
    public function getTotalSuspended()
    {
        return $this->withCount(['companies' => function ($query) {
            $query->where('is_suspend',SUSPENDED);
        }]);
    }

    /*** Get Active Hours */
    public function getActiveHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',ACTIVE)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Idle Hours */
    public function getIdleHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',IDLE)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Meeting Idle Hours */
    public function getMeetingHoursAttribute()
    {
        $tracked_time = $this->session->where('status_id',MEETING)->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Total Hours */
    public function getTotalHoursAttribute()
    {
        $tracked_time = $this->session->sum('total_session_time');
        return sprintf('%dh %dm', $tracked_time / 3600, floor($tracked_time / 60) % 60);
    }

    /*** Get Companies User Allowed To Access */
    public function getAccessableCompaniesAttribute()
    {
        return $this->companies()->wherePivot('is_suspend',false)->get();
    }

    /*** Get If Current User Is Root Account On Current Workshop */
    public function getIsRootAttribute()
    {
        return Company::where('user_id',Auth::user()->id)->count();
    }

    /*** Get If Current User Is Suspended On Current Workshop */
    public function getIsSuspendAttribute()
    {
        return $this->companies()->wherePivot('is_suspend',1)->where('companies.id',Auth::user()->active_company_id)->count();
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class)->withPivot(['is_suspend']);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function activeCompany()
    {
        return $this->belongsTo(Company::class,'active_company_id');
    }

    public function session()
    {
        return $this->hasMany(Session::class);
    }
}
