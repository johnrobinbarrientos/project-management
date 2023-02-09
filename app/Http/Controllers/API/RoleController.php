<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\Module;
use App\Models\User;
use App\Models\Role;


class RoleController extends Controller
{

    public function index()
    {
        $rows = Role::all();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function store()
    {
        $title = request()->title;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }

        $new = new Role;
        $new->title = $title;
        $new->save();

        $modules = Module::all();

        foreach ($modules as $module) {
            $module_role = new ModuleRole();
            $module_role->module_id = $module->id;
            $module_role->role_id = $new->id;
            $module_role->C = false;
            $module_role->R = true;
            $module_role->U = false;
            $module_role->D = false;
            $module_role->save();
        }

        return response(['success' => 1, 'message' => 'New role has been added!']);
    }

    public function update($id)
    {
        $title = request()->title;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a title...'],500);
        }

        $data = Role::find($id);
        $data->title = $title;
        $data->save();

        return response(['success' => 1, 'message' => $data->title.' role has been updated!']);
    }
   
}
