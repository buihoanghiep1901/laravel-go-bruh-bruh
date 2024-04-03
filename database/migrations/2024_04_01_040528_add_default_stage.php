<?php

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\StageExample;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $jobs= Job::query()->get();
        $stages= StageExample::query()->orderBy('id')->get();

        $data=[];
        foreach ($jobs as $job){
            foreach ($stages as $stage){
                $data[]=[
                    'job_id'=> $job->id,
                    'name'=> $stage->name,
                    'position'=> $stage->id,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ];
            }
        }
        DB::table('job_stages')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('job_stages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
