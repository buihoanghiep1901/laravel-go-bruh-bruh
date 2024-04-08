<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
//    use SoftDeletes;

    protected $fillable = ['title', 'created_by', 'content'];

//    protected $casts = [
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime'
//    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(EmailFile::class, 'email_template_id');
    }
}
