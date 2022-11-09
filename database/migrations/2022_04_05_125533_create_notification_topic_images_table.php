<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTopicImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_topic_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_topic_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('notification_topic_id')->references('id')->on('notification_topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_topic_images', function (Blueprint $table) {
            $table->dropForeign('notification_topic_id');
        });
        Schema::dropIfExists('notification_topic_images');
    }
}
