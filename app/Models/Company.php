<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Events\WebhookReceived;

class Company extends Model
{
    use HasFactory,Billable;

    protected $guarded = [];

    public function register(array $request)
    {
        // Create the company
        $company = self::create([
            'name'  => $request['name'],
            'email' => $request['email'],
            'country' => $request['country'],
            'address' => $request['address'],
            'description' => $request['description'] ?? null,
            'seats' => $request['seats'],
            'active' => false
        ]);

        // Create main root user
        $rootUser = User::generateRootUser($request['user']);
        $token =  $rootUser->login($rootUser->email, $request['user']['password'])['token'];

        // Payment intent
        $company
        ->newSubscription('bronze_plan',['price_1N43m6KV5l49k0XZkSKy9RHQ'])
        ->trialDays(10)
        ->quantity(5)
        ->create($request['billing']['id'], [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);

        return [
            'company' => $company,
            'user'    => $rootUser,
            'token'   => $token
        ];
    }

    // Relations
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
