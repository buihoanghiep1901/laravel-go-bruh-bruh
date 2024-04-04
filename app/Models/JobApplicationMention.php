<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationMention extends Model
{


    protected $fillable = ['job_application_id', 'type', 'mention_id'];

}
