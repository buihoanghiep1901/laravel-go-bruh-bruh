<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('job_applications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $jobs=Job::query()->get();
        foreach ($jobs as $job){
            JobApplication::factory(5)->create([
                'job_id'=> $job->id,
                'stage_id'=> $job->jobStages()->first()->id,
            ]);
        }
    }
}
