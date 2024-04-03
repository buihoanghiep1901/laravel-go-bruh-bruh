<?php

namespace Database\Seeders;

use App\Models\JobType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

       JobType::firstOrCreate(['name' => 'IT']);
       JobType::firstOrCreate(['name' => 'Non IT']);
    }
}
