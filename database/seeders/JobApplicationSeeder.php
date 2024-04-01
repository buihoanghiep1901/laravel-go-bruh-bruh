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
        DB::statement("ALTER TABLE `job_applications` AUTO_INCREMENT = 1");
        DB::table('job_applications')->delete();
        $jobs=Job::query()->get();
        $stage=$jobs->first()->jobStages()->first()->id;
//        dd($stage);
        foreach ($jobs as $job){
            JobApplication::factory(5)->create([
                'job_id'=> $job->id,
                'stage_id'=> $job->jobStages()->first()->id,
            ]);
        }
    }
}
