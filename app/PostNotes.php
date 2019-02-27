<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostNotes extends Model
{

    use SoftDeletes;

    protected $table = 'post_notes';

    protected $fillable = [
        'post_id',
        'user_id',
        'content'
    ];

}
