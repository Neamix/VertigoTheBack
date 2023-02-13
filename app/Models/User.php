<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\Role;

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

    static function login($request)
    {
        $user = User::where('email', $request['email'])->first();
        
        if(! $user || ! Hash::check($request['password'], $user->password))
        {
            throw ValidationException::withMessages(['Invalid login']);
        }

        return $user->createToken('login token')->plainTextToken;
    }

    // Scopes

    public function scopeFilter($query,$request)
    {
        if ( isset($request["input"]['name']) ) {
            $query->where('name','like','%'.$request["input"]['name'].'%');
        }

        return $query;
    }

    //Relations

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
