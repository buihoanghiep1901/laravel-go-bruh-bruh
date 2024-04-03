<?php

namespace App\Models;

use App\Services\S3Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    public const FOLDER = 'resumes';
    protected $fillable = ['full_name', 'email', 'job_id', 'stage_id'];
    protected $appends = ['resume_url'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function stage()
    {
        return $this->belongsTo(JobStage::class, 'stage_id');
    }

    public function resumes()
    {
        return $this->hasMany(JobApplicationResume::class, 'job_application_id');
    }

    public function getResumeUrlAttribute(): false|array
    {
        $resumes = JobApplicationResume::query()->where('job_application_id', $this->id)->get()->pluck('resume')->toArray();
//        dd($resumes);
        if ($resumes) {
            $resume_url = [];
            $s3 = new S3Service();
            foreach ($resumes as $resume) {
                $resume_url[] = $s3->getPrivateFile($resume);
            }
            return $resume_url;
        }
        return  false;
    }

}
