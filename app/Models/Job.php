<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobStages(): HasMany
    {
        return $this->hasMany(JobStage::class, 'job_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id', 'id');
    }

    public function jobMentions(): HasMany
    {
        return $this->hasMany(JobMention::class, 'job_id');
    }

    public function mentionGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            MentionGroup::class,
            'job_mentions',
            'job_id',
            'mention_id')
            ->where('job_mentions.type', 'group');
    }

    public function mentionUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'job_mentions',
            'job_id',
            'mention_id')
            ->where('job_mentions.type', 'user');
    }
}

