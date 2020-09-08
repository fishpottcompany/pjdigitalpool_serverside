<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('user_surname', 255);
            $table->string('user_firstname', 255);
            $table->string('user_country', 255);
            $table->string('user_phone_number', 255)->unique();
            $table->string('user_email', 255)->unique();
            $table->string('password', 255);
            $table->text('user_scope');
            $table->boolean('user_flagged');
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
