<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('sprints_yes')->default(1);
            $table->tinyInteger('milestones_yes')->default(1);
            $table->tinyInteger('ideas_yes')->default(1);
            $table->tinyInteger('retrospectives_yes')->default(1);
            $table->tinyInteger('timesheet_yes')->default(1);
            $table->tinyInteger('chat_yes')->default(1);
            $table->bigInteger('theme_id')->nullable();
            $table->bigInteger('board_id')->nullable();
            $table->Integer('sort_index')->nullable();
            $table->Integer('duration')->nullable();
            $table->date('project_start_date');
            $table->date('project_due_date');
            $table->date('project_completed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
