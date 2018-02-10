<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLadderCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ladder_characters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('league');
            $table->integer('rank');
            $table->string('name');
            $table->string('class');
            $table->integer('level');
            $table->text('items_most_sockets')->nullable();
            $table->integer('account_id')->unsigned();
            $table->boolean('dead')->default(false);// default false
            $table->boolean('public')->default(false);//  default false
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
        Schema::drop('ladder_characters');
    }
}
