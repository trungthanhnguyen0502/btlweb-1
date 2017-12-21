<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePasswordResetKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_reset_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('security_key');
            $table->integer('request_time');
            $table->ipAddress('ip_address');
            $table->string('browser');
            $table->string('platform');
            $table->integer('requested_at');
            $table->integer('expired_at');
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
        Schema::dropIfExists('password_reset_keys');
    }
}
