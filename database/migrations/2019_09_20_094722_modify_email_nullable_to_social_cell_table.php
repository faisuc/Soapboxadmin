<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEmailNullableToSocialCellTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_cell', function (Blueprint $table) {
            $table->dropColumn('email_owner');
            $table->dropColumn('email_marketer');
            $table->dropColumn('email_client');
        });
        Schema::table('social_cell', function (Blueprint $table) {
            $table->string('email_owner')->nullable()->after('cell_name');
            $table->string('email_marketer')->nullable()->after('email_owner');
            $table->string('email_client')->nullable()->after('email_marketer');
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_cell', function (Blueprint $table) {
            //
        });
    }
}
