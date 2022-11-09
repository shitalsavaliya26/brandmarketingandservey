<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToPollTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_titles', function (Blueprint $table) {
            //
            $table->double('total_cost')->default(0)->after('is_deleted');
            $table->double('total_cost_usd')->default(0)->after('total_cost');
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
