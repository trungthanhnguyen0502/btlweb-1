<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password');
            $table->string('picture')->default('');
            $table->tinyInteger('gender');
            $table->integer('birthday');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('display_name');
            $table->string('title');
            $table->integer('team_id');
            $table->tinyInteger('role');
            $table->string('role_title');
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
        Schema::dropIfExists('employees');
    }
}
