<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('project_id')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('milestone_id')->nullable();
            $table->bigInteger('sprint_id')->nullable();
            $table->bigInteger('lead_id')->nullable();
            $table->bigInteger('item_type_id')->nullable();
            $table->bigInteger('status_id')->nullable();
            $table->bigInteger('effort_id')->nullable();
            $table->bigInteger('priority_id')->nullable();
            $table->bigInteger('parent_task_id')->nullable();
            $table->bigInteger('dept_id')->nullable();
            $table->bigInteger('changed_by_id')->nullable();
            $table->date('task_due_date')->nullable();
            $table->date('task_start')->nullable();
            $table->date('task_finish')->nullable();
            $table->Integer('task_duration')->nullable();
            $table->bigInteger('change_date_id')->nullable();
            $table->bigInteger('board_id')->nullable();
            $table->Integer('sort_index')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
