<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('jobs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
       Job::factory()->count(10)->create();
    }
}
