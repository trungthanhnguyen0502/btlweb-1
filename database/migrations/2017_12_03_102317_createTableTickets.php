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
            $table->unsignedInteger('created_by');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('out_of_date')->default(0);
            $table->tinyInteger('priority');
            $table->dateTime('deadline');
            $table->unsignedInteger('assigned_to')->default(0);
            $table->integer('rating')->nullable();
            $table->integer('attachment')->default(0);
            $table->tinyInteger('team_id');
            $table->integer('resolved_at')->default(0);
            $table->integer('closed_at')->default(0);
            $table->integer('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')->on('employees');

            $table->foreign('assigned_to')
                ->references('id')->on('employees');

            $table->foreign('team_id')
                ->references('id')->on('teams');
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
