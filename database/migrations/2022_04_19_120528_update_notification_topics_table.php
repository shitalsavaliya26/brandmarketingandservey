<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_topics', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
            $table->enum('attachment_type', ['image', 'pdf','link'])->nullable()->after('slug');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_topics', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
}
