<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocialAccountInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_accounts_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('type_id');
            $table->string('name');
            $table->unsignedInteger('fb_talking_about_count');
            $table->unsignedInteger('fb_fan_count');
            $table->unsignedInteger('fb_rating_count');
            $table->unsignedInteger('fb_published_posts_count');
            $table->unsignedInteger('twt_followers_count');
            $table->unsignedInteger('twt_following_count');
            $table->unsignedInteger('twt_likes_count');
            $table->unsignedInteger('twt_posts_count');
            $table->unsignedInteger('insta_followers_count');
            $table->unsignedInteger('insta_following_count');
            $table->unsignedInteger('insta_likes_count');
            $table->unsignedInteger('insta_posts_count');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::dropIfExists('social_accounts_info');
    }
}
