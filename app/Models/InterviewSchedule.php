<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewSchedule extends Model
{


    protected $fillable = ['job_application_id', 'schedule_date', 'status'];

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'interview_schedule_employees', 'interview_schedule_id', 'user_id');
    }
}
