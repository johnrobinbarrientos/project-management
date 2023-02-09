<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\Module;
use App\Models\ModuleRole;
use App\Models\Role;
use App\Models\Board;
use App\Models\Status;


class ModuleController extends Controller
{

    public function index()
    {
        $keyword = request()->keyword;


        $rows = Module::where('id','>',0);

        if (!empty($keyword)) {
            $rows = $rows->where('name','LIKE','%'.$keyword.'%');
        }

        $rows = $rows->get();

        return response(['success' => 1, 'rows' => $rows]);
    }


    public function getDetailsByKey()
    {
        $key = request()->key;

        $data = Module::where('name','=',$key)->first();
        return response(['success' => 1, 'data' => $data]);
    }


    public function store()
    {
        $name = request()->name;
        $key = request()->key;

        if (empty($name)) {
            return response(['success' => 0, 'message' => 'Please enter a module name...'],500);
        }

        if (empty($key)) {
            return response(['success' => 0, 'message' => 'Please enter a module key...'],500);
        }

        $new = new Module;
        $new->name = $name;
        $new->key = $key;
        $new->save();

        $roles = Role::all();

        foreach ($roles as $role) {
            $module_role = new ModuleRole();
            $module_role->module_id = $new->id;
            $module_role->role_id = $role->id;
            $module_role->C = false;
            $module_role->R = true;
            $module_role->U = false;
            $module_role->D = false;
            $module_role->save();
        }

        return response(['success' => 1, 'message' => 'New module has been added!']);
    }


    public function update($id)
    {
        $name = request()->name;
        $key = request()->key;

        if (empty($name)) {
            return response(['success' => 0, 'message' => 'Please enter a module name...'],500);
        }

        if (empty($key)) {
            return response(['success' => 0, 'message' => 'Please enter a module key...'],500);
        }

        $data = Module::find($id);
        $data->name = $name;
        $data->key = $key;
        $data->save();

        return response(['success' => 1, 'message' => $data->name.' module has been updated!']);
    }

    public function getModuleRoles($module_id)
    {
        $roles = Role::all();
        $module = Module::find($module_id);

        foreach ($roles as $role) {
            $module_role = ModuleRole::where('module_id','=',$module->id)->where('role_id','=',$role->id)->first();

            if (!$module_role) {
                $module_role = new ModuleRole();
                $module_role->module_id = $module->id;
                $module_role->role_id = $role->id;
                $module_role->C = false;
                $module_role->R = true;
                $module_role->U = false;
                $module_role->D = false;
                $module_role->save();
            } else {
                $module_role->C = ($module_role->C) ? true : false;
                $module_role->R = ($module_role->R) ? true : false;
                $module_role->U = ($module_role->U) ? true : false;
                $module_role->D = ($module_role->D) ? true : false;
            }

            $role->module_role = $module_role;
        }
        
        return response(['success' => 1, 'rows' => $roles]);
    }

    public function createModuleBoard($module_id)
    {
        $title = request()->title;
        $module_id = request()->module_id;
        $project_id = request()->project_id;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a board name...'],500);
        }

        if (empty($module_id)) {
            return response(['success' => 0, 'message' => 'An error occur while processing...'],500);
        }


        $count = Board::where('module_id','=',$module_id)->where('project_id','=',$project_id)->where('is_default','=',true)->get()->count();
        

        $is_default = ($count < 1) ? true : false;
 
        $total_boards = Board::where('module_id','=',$module_id)->get()->count();

        $board = new Board();
        $board->title = $title;
        $board->module_id = $module_id;
        $board->project_id = $project_id;
        $board->is_default = $is_default;
        $board->order = $total_boards + 1;
        $board->save();

        return response(['success' => 1, 'message' => $board->title.' board has been added!']);
    }

    public function createModuleStatus($module_id)
    {
        $title = request()->title;
        $module_id = request()->module_id;

        if (empty($title)) {
            return response(['success' => 0, 'message' => 'Please enter a status name...'],500);
        }

        if (empty($module_id)) {
            return response(['success' => 0, 'message' => 'An error occur while processing...'],500);
        }


        $count = Status::where('module_id','=',$module_id)->where('is_default','=',true)->get()->count();
 

        $status = new Status();
        $status->title = $title;
        $status->module_id = $module_id;
        $status->save();

        return response(['success' => 1, 'message' => $status->title.' status has been added!']);
    }

    public function getModuleBoards($module_id)
    {
        $boards = Board::where('module_id','=',$module_id)->get();
        return response(['success' => 1, 'rows' => $boards]);
    }

    public function getModuleStatus($module_id)
    {
        $status = Status::where('module_id','=',$module_id)->get();
        return response(['success' => 1, 'rows' => $status]);
    }


    public function saveModuleRoles()
    {
        $roles = request()->roles;
        
        foreach ($roles as $role) {

            $module_role =  (object) $role['module_role']; 
        
            $exists = ModuleRole::where('module_id','=',$module_role->module_id)->where('role_id','=',$module_role->role_id)->first();
            if ($exists) {
                $exists->C = $module_role->C;
                $exists->R = $module_role->R;
                $exists->U = $module_role->U;
                $exists->D = $module_role->D;
                $exists->save();
            }
            
        }
        
        return response(['success' => 1, 'message' => 'Module role has been updated!']);
    }


    public function sortModuleBoards($module_id)
    {
        $boards = request()->boards;
        
        $count = 0;

        foreach ($boards as $board) {
            $board_id = $board['id'];
            $found = Board::where('id','=',$board_id)->where('module_id','=',$module_id)->first();
            
            if (!$found) {
                continue;
            }

            $found->order = $count;
            $found->save();

            $count++;
        }

        $boards = Board::where('module_id','=',$module_id)->with('tasks')->orderBy('order')->get();
        
        return response(['success' => 1, 'rows' => $boards]);
    }

    public function getUserSettings()
    {
        $auth = Auth::user();
        $module_roles = ModuleRole::where('role_id','=',$auth->role_id)->with('module')->get();
        $module_user_settings = [];

        foreach ($module_roles as $key => $value) {
            $module_name = $value['module']['name'];
            $module_user_settings[$module_name] = [];
            $module_user_settings[$module_name]['WRITE'] = $value['C'];
            $module_user_settings[$module_name]['READ'] = $value['R'];
            $module_user_settings[$module_name]['EDIT'] = $value['U'];
            $module_user_settings[$module_name]['DELETE'] = $value['D'];
        }

        return response(['success' => 1, 'rows' => $module_user_settings, 'a' => $module_roles]);
    }
   
}
