<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionGroupUser extends Model
{

    protected $fillable = ['mention_group_id', 'user_id'];

}
