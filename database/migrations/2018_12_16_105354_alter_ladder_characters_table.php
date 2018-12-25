<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLadderCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ladder_characters', function($table) {
            $table->string('unique_id');
            $table->bigInteger('experience');
            $table->boolean('online')->default(false);
            $table->text('stats')->nullable();
            $table->integer('delve_default');
            $table->integer('delve_solo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ladder_characters', function($table) {
            $table->dropColumn('unique_id');
            $table->dropColumn('experience');
            $table->dropColumn('online');
            $table->dropColumn('stats');
            $table->dropColumn('delve_default');
            $table->dropColumn('delve_solo');
        });
    }
}
