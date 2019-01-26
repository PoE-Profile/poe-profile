<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexLadder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function($table) {
            $table->boolean('indexed')->default(false);
        });

        Schema::table('ladder_characters', function($table) {
            $table->boolean('retired')->default(false);

            //add indexes for fast update
            $table->index('delve_default');
            $table->index('delve_solo');
            $table->index('online');
            $table->index('unique_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leagues', function($table) {
            $table->dropColumn('indexed');
        });

        Schema::table('ladder_characters', function (Blueprint $table) {
            $table->dropColumn('retired');

            $table->dropIndex(['delve_default']);
            $table->dropIndex(['delve_solo']);
            $table->dropIndex(['online']);
            $table->dropIndex(['unique_id']);
        });

    }
}
