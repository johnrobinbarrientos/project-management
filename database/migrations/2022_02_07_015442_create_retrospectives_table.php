<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetrospectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retrospectives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('milestone_id')->nullable();
            $table->bigInteger('sprint_id')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('retro_type')->nullable();
            $table->bigInteger('status_id')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('board_id')->nullable();
            $table->Integer('sort_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retrospectives');
    }
}
