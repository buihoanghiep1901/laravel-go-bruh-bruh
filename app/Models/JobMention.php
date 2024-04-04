<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobMention extends Model
{


    protected $fillable = ['job_id', 'type', 'mention_id'];

}
