<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('provider_name')->nullable()->after('country_id');
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->string('contact_number')->nullable()->change();


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
            $table->dropColumn(['provider_name', 'provider_id']);
            $table->dropUnique(['contact_number']);
            $table->unique(['contact_number']);
        });
    }
}
