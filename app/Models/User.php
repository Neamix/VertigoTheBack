<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Crud Function 
    static function upsertInstance($request)
    {
        return User::create([
            'name'  => $request['input']['name'],
            'email' => $request['input']['email'],
            'role_id' => 1
        ]);
    }

    // Scopes

    public function scopeFilter($query,$request)
    {
        if ( isset($request["input"]['name']) ) {
            $query->where('name','like','%'.$request["input"]['name'].'%');
        }

        return $query;
    }
}
