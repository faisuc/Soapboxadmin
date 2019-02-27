<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ImageRepository extends Model
{

    use SoftDeletes;

    protected $table = 'image_repositories';

    protected $fillable = [
        'user_id',
        'type_id',
        'file_name',
        'file_ext'
    ];

    public function getImageURLAttribute()
    {
        if ($this->file_name)
        {
            return Storage::url('public/medias/images/' . $this->file_name);
        }
        else
        {
            return asset('assets/images/defaultnoimage.png');
        }
    }

}
