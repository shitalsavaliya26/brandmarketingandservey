<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithdrawHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdraw_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->default(0);
            $table->double('amount')->default(0);
            $table->double('amount_usd')->default(0);
            $table->integer("currency_id")->nullable();
            $table->string('currency')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('acc_holder_name')->nullable();
            $table->unsignedBigInteger('acc_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->enum('status', ["pending","approved","rejected"])->default("pending");
            $table->text('reason')->comment('reason of unsuccessful transaction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_withdraw_histories');
    }
}
