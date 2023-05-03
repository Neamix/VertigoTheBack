<?php

namespace App\Models;

use App\Services\MailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
