<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::updateOrCreate([
            'id' => 1
        ],[
            'id'   => 1,
            'name' => "Arktic Solution LLC",
            'country_id' => 1,
            'zip_code' => 55555,
            'work_full_day' => true
        ]);
    }
}
