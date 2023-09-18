<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Scopes
    public function scopeFilter($query,$request) 
    {
        if ( isset($request['name']) ) {
            $query->where('name','like','%'.$request['name'].'%');
        }

        return $query;
    }

    // Attributes
    public function getCreatedDateAttribute()
    {
        return date('d M Y',strtotime($this->created_at));
    }

    // Relations
    public function inputs()
    {
        return $this->hasMany(Input::class);
    }

    public function accessableMembers()
    {
        return $this->belongsToMany(User::class);
    }
}
