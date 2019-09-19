<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialCellTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_cell', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cell_name');
            $table->string('email_owner');
            $table->string('email_marketer');
            $table->string('email_client');
            $table->unsignedInteger('payment_status')->default(1);
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
        Schema::dropIfExists('social_cell');
    }
}
