<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewTemplate extends Model
{
//    use SoftDeletes;
    public const FOLDER = 'interview_template';
    protected $table = 'interview_templates';
    protected $fillable = ['created_by', 'title', 'content', 'note'];

//    protected $casts = [
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime'
//    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(InterviewFile::class, 'interview_template_id');
    }
}
