<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('milestone_id')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->date('sprint_start_date')->nullable();
            $table->date('sprint_due_date')->nullable();
            $table->bigInteger('board_id')->nullable();
            $table->Integer('sort_index')->nullable();
            $table->Integer('sprint_duration')->nullable();
            $table->bigInteger('depends_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sprints');
    }
}
