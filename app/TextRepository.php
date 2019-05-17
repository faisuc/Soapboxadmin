<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TextRepository extends Model
{

    use SoftDeletes;

    protected $table = 'text_repositories';

    protected $fillable = [
        'user_id',
        'title',
        'content'
    ];

}
