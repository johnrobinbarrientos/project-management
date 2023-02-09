<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('uuid')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('role_id')->nullable();
            $table->string('username')->nullable();
            $table->string('password');
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->tinyInteger('sms_ok')->default(1);
            $table->bigInteger('user_type_id')->nullable();
            $table->string('status')->default('unverified');
            $table->string('verification_code')->nullable();
            $table->string('forgot_password_code')->nullable();
            $table->timestamp('last_logged_in')->nullable();
            $table->string('time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
