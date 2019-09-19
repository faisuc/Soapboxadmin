<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialCell extends Model
{

    use SoftDeletes;

    protected $table = 'social_cell';

    protected $fillable = [
        'cell_name',
        'email_owner',
        'email_marketer',
        'email_clients',
        'payment_status'        
    ];

    protected $dates = ['deleted_at'];

}
