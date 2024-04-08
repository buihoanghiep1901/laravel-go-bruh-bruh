<?php

namespace App\Models;

use App\Services\S3Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewFile extends Model
{
//    use SoftDeletes;
    protected $table = 'interview_files';
    protected $fillable = ['interview_template_id', 'url'];

//    protected $casts = [
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime'
//    ];

    protected $appends = [
        'file_url'
    ];

    public function getFileUrlAttribute()
    {
        $service = new S3Service();
        if (is_null($this->url)) {
            return null;
        }
        return $service->getPrivateFile($this->url);
    }
}
