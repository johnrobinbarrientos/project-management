<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules');
            
            $table->boolean('C')->default(false);
            $table->boolean('R')->default(false);
            $table->boolean('U')->default(false);
            $table->boolean('D')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_roles');
    }
}
