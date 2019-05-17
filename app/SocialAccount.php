<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccount extends Model
{

    use SoftDeletes;

    protected $table = 'social_accounts';

    protected $fillable = [
        'user_id',
        'type_id',
        'name',
        'url'
    ];

    protected $dates = ['deleted_at'];

}
