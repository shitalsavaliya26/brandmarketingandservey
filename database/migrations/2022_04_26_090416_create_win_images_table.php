<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWinImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('win_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('win_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('win_id')->references('id')->on('wins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('win_images', function (Blueprint $table) {
            $table->dropForeign('win_id');
        });
        Schema::dropIfExists('win_images');
    }
}
