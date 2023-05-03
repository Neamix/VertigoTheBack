<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate([
            'id' => 1
        ],[
            'id' => 1,
            'name' => "Abdalrhman Hussin",
            'password' => Hash::make('AG616433'),
            'email' => "abdalrhmanhussin44@gmail.com",
            'email_verified_at' => now(),
            'type' => 1
        ]);

        $user->companies()->syncWithoutDetaching([1]);
    }
}
