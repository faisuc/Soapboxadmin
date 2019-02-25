<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Post extends Model
{

    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'featured_image_id',
        'link',
        'is_posted',
        'status',
        'schedule_to_post_date',
    ];

    protected $dates = ['deleted_at'];

    public function media()
    {
        return $this->hasOne('App\Media', 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }

    public function getScheduleToPostDateAttribute()
    {
        return Carbon::createFromTimestamp(strtotime($this->attributes['schedule_to_post_date']))->format('Y-m-d g:i A');
    }

    public function getFeaturedImageAttribute()
    {
        if ($this->featured_image_id)
        {
            $media = $this->media->find($this->featured_image_id);
            return Storage::url('public/medias/images/' . $media->file_name);
        }
        else
        {
            return asset('assets/images/defaultnoimage.png');
        }
    }

}
