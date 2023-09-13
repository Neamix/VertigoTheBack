<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

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
