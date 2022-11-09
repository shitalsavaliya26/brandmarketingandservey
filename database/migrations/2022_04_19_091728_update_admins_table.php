<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('status', [0, 1])->default(1)->comment('0:inactve, 1:active')->after('country_id');
            $table->enum('pause_account', [0, 1])->nullable()->comment('0:deactivate by admin, 1:deactivate by user')->after('status');
            $table->double('wallet_amount')->default(0);
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
            $table->dropColumn(['status', 'pause_account']);
        });
    }
}
