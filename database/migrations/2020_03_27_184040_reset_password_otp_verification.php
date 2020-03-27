<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ResetPasswordOtpVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reset_password_otp_verification', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger("otp_id")->unsigned();
            $table->foreign('otp_id')->references('id')->on('reset_password_otp');
            $table->bigInteger("user_id")->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string("token");
            $table->integer("status");

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
        Schema::dropIfExists('reset_password_otp_verification');
    }
}
