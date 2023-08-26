<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::firstOrCreate([
            'id' => 1,
            'name' => 'Active'
        ]);

        Status::firstOrCreate([
            'id' => 2,
            'name' => 'Idle'
        ]);

        Status::firstOrCreate([
            'id' => 3,
            'name' => 'Do not disturb'
        ]);

        Status::firstOrCreate([
            'id' => 4,
            'name' => 'Offline'
        ]);
    }
}
