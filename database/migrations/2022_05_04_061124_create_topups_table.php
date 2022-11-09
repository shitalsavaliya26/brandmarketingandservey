<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->double('amount')->default(0);
            $table->double('fees')->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('charge_id')->nullable();
            $table->enum('status', [0, 1])->default(1)->comment('0:unsuccessful transaction, 1:successful transaction');
            $table->text('reason')->comment('reason of unsuccessful transaction')->nullable();
            $table->text('response_data')->comment('JSON response from stripe charge')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscriber_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->dropForeign(['subscriber_id']);
        });
        Schema::dropIfExists('topups');
    }
}
