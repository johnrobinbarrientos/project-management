<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('project_id');
            $table->string('title');
            $table->bigInteger('status_id');
            $table->date('milestone_start');
            $table->date('milestone_finish');
            $table->bigInteger('finish_by');
            $table->bigInteger('depends_id');
            $table->bigInteger('group_id');
            $table->bigInteger('board_id');
            $table->Integer('sort_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('milestones');
    }
}
