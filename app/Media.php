<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{

    use SoftDeletes;

    protected $table = 'medias';

    protected $fillable = [
        'user_id',
        'type_id',
        'file_name',
        'file_ext'
    ];

    protected $dates = ['deleted_at'];

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

}
