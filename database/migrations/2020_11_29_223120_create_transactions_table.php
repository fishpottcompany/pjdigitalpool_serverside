<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->string('transaction_ext_id', 255)->unique();
            $table->unsignedBigInteger('amount');
            $table->longText('reference');
            $table->string('payment_type', 255)->default("[not set]");
            $table->string('status', 255)->default("[not set]");
            $table->longText('status_description');
            $table->timestamps();
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('transactions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
