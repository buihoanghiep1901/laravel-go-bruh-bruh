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
        // User::factory(10)->create();
        DB::statement("ALTER TABLE `job_types` AUTO_INCREMENT = 1");
        DB::table('job_types')->delete();
       JobType::create(['name' => 'IT']);
       JobType::create(['name' => 'Non IT']);
    }
}
