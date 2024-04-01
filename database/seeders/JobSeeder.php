<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement("ALTER TABLE `jobs` AUTO_INCREMENT = 1");
        DB::table('jobs')->delete();
       Job::factory()->count(10)->create();
    }
}
