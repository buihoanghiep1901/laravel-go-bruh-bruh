<?php

namespace App\Models;

use App\Services\S3Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    public const FOLDER = 'resumes';
    protected $fillable = ['full_name', 'email', 'job_id', 'stage_id'];
    protected $appends = ['resume_url'];

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

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(JobStage::class, 'stage_id');
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(JobApplicationResume::class, 'job_application_id');
    }

    public function jobApplicationMentions(): HasMany
    {
        return $this->hasMany(JobApplicationMention::class, 'job_application_id');
    }

    public function mentionGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            MentionGroup::class,
            'job_application_mentions',
            'job_application_id',
            'mention_id')
            ->where('job_application_mentions.type', 'group');
    }

    public function mentionUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'job_application_mentions',
            'job_application_id',
            'mention_id')
            ->where('job_application_mentions.type', 'user');
    }

}
