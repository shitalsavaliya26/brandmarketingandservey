<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->double('bonus_amount')->default(0)->after('wallet_amount_usd');
            $table->double('bonus_amount_usd')->default(0)->after('bonus_amount');
            $table->enum('is_bonus_used', ['0','1'])->default('0')->after('bonus_amount_usd');
            $table->enum('is_bonus_added', ['0','1'])->default('0')->after('is_bonus_used');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            //
        });
    }
}
