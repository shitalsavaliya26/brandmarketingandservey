<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->double('amount')->default(0);
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('acc_holder_name')->nullable();
            $table->unsignedBigInteger('acc_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->enum('status', [0, 1, 2])->default(2)->comment('0:unsuccessful transaction, 1:successful transaction, 2:pending transaction');
            $table->text('reason')->comment('reason of unsuccessful transaction')->nullable();
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
        Schema::table('withdraw_histories', function (Blueprint $table) {
            $table->dropForeign(['subscriber_id']);
        });
        Schema::dropIfExists('withdraw_histories');
    }
}
