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
