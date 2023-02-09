<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Module;
use App\Models\ModuleRole;
use App\Models\Role;

class ModuleRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $module = new Module;
        $module->name = 'Modules';
        $module->key = 'modules';
        $module->save();

        $role = new Role;
        $role->title = 'Administrator';
        $role->save();

        $module_role = new ModuleRole();
        $module_role->module_id = $module->id;
        $module_role->role_id = $role->id;
        $module_role->C = true;
        $module_role->R = true;
        $module_role->U = true;
        $module_role->D = true;
        $module_role->save();
    }
}
