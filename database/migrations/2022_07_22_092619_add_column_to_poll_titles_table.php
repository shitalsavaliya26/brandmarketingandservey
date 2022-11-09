<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPollTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_titles', function (Blueprint $table) {
            $table->integer("total_quantity")->nullable()->after('total_questions');
            $table->integer("remaining_quantity")->nullable()->after('total_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_titles', function (Blueprint $table) {
            //
        });
    }
}
