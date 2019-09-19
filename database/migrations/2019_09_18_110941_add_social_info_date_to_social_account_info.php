<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialInfoDateToSocialAccountInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_accounts_info', function (Blueprint $table) {            
            
            // $table->unsignedInteger('social_id')->after('type_id');
            $table->date('social_info_date')->after('insta_posts_count');
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
