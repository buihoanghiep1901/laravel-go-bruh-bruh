<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionGroup extends Model
{


    protected $fillable = [
        'name',
    ];

    public function mentionGroups()
    {
        return $this->hasMany(MentionGroupUser::class, 'mention_group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'mention_group_users', 'mention_group_id', 'user_id');
    }
}
