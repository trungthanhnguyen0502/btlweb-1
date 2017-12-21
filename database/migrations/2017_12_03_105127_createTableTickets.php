<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject', 255);
            $table->mediumText('content');
            $table->integer('created_by')->foreign('id')->reference('id')->on('users');
            $table->tinyInteger('status');
            $table->tinyInteger('priority');
            $table->integer('deadline');
            $table->integer('assigned_to');
            $table->integer('rating')->nullable();
            $table->integer('team_id');
            $table->integer('resolved_at')->default(0);
            $table->integer('closed_at')->default(0);
            $table->integer('deleted_at')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
