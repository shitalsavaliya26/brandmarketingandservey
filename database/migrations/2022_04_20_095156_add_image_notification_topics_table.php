<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageNotificationTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_topics', function (Blueprint $table) {
            $table->string('image')->nullable()->after('attachment_type');
            $table->timestamp('notify_date')->after('image')->useCurrent();
            $table->enum('notify_flag', [0, 1])->default(1)->after('notify_date');
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
            //
        });
    }
}
