<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicationResume extends Model
{
    use HasFactory;
    protected $fillable=['name', 'resume', 'created_at', 'updated_at'];
    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }
}
