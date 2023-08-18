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

    /*** Get created month */
    public function getCreatedMonthAttribute()
    {
        return date('M',strtotime($this->start_date));
    }
}
