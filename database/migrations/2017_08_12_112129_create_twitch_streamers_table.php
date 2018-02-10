<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwitchStreamersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitch_streamers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('account_id')->unsigned();
            $table->integer('viewers')->default(0);
            $table->integer('fallowers')->default(0);
            $table->string('status')->default('');
            $table->integer('channel_id')->default(0);
            $table->string('img_preview')->default('');
            $table->boolean('online')->default(false);
            $table->dateTime('last_online')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('twitch_streamers');
    }
}
