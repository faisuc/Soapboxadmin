<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialAccountInfoChangeToSocialAccountsInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_accounts_info', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('fb_talking_about_count');
            $table->dropColumn('fb_fan_count');
            $table->dropColumn('fb_rating_count');
            $table->dropColumn('fb_published_posts_count');
            $table->dropColumn('twt_followers_count');
            $table->dropColumn('twt_following_count');
            $table->dropColumn('twt_likes_count');
            $table->dropColumn('twt_posts_count');
            $table->dropColumn('insta_followers_count');
            $table->dropColumn('insta_following_count');
            $table->dropColumn('insta_likes_count');
            $table->dropColumn('insta_posts_count');
            $table->dropColumn('social_info_date');
        });
        Schema::table('social_accounts_info', function (Blueprint $table) {
            $table->string('name')->nullable()->after('social_id');
            $table->unsignedInteger('fb_talking_about_count')->nullable()->after('name');
            $table->unsignedInteger('fb_fan_count')->nullable()->after('fb_talking_about_count');
            $table->unsignedInteger('fb_rating_count')->nullable()->after('fb_fan_count');
            $table->unsignedInteger('fb_published_posts_count')->nullable()->after('fb_rating_count');
            $table->unsignedInteger('twt_followers_count')->nullable()->after('fb_published_posts_count');
            $table->unsignedInteger('twt_following_count')->nullable()->after('twt_followers_count');
            $table->unsignedInteger('twt_likes_count')->nullable()->after('twt_following_count');
            $table->unsignedInteger('twt_posts_count')->nullable()->after('twt_likes_count');
            $table->unsignedInteger('insta_followers_count')->nullable()->after('twt_posts_count');
            $table->unsignedInteger('insta_following_count')->nullable()->after('insta_followers_count');
            $table->unsignedInteger('insta_likes_count')->nullable()->after('insta_following_count');
            $table->unsignedInteger('insta_posts_count')->nullable()->after('insta_likes_count');
            $table->date('social_info_date')->nullable()->after('insta_posts_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_accounts_info', function (Blueprint $table) {
            //
        });
    }
}
