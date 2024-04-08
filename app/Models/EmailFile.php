<?php

namespace App\Models;

use App\Services\S3Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailFile extends Model
{
//    use SoftDeletes;

    const FOLDER_S3 = 'email_template_file';
    protected $fillable = ['email_template_id', 'url'];

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
