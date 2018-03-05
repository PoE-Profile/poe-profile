<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snapshots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash')->unique();
            $table->text('tree_data');
            $table->text('item_data');
            $table->string('poe_version');
            $table->string('original_char')->nullable();
            $table->string('original_level')->nullable();
            $table->dateTime('last_visit')->nullable();
            $table->timestamps();
        });

        Schema::table('snapshots', function($table) {
            $table->index('hash');
            $table->index('original_char');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snapshots');
    }
}
