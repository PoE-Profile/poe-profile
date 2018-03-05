<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('name');
            $table->string('guild')->default('');
            $table->string('poe_avatar_url')->default('');
            $table->integer('challenges_completed')->default(0);
            $table->string('last_character')->default("");
            $table->text('last_character_info')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts');
    }
}
