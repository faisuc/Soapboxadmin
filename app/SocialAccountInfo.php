<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAccountInfo extends Model
{

    use SoftDeletes;

    protected $table = 'social_accounts_info';

    protected $fillable = [
        'user_id',
        'type_id',
        'social_id',
        'name',
        'fb_talking_about_count',
        'fb_fan_count',
        'fb_rating_count',
        'fb_published_posts_count',
        'twt_followers_count',
        'twt_following_count',
        'twt_likes_count',
        'twt_posts_count',
        'insta_followers_count',
        'insta_following_count',
        'insta_likes_count',
        'insta_posts_count',
        'social_info_date'
    ];

    protected $dates = ['deleted_at'];
}
