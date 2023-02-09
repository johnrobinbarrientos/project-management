<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Module;
use App\Models\ModuleRole;

use Auth; 

class MenuController extends Controller
{
    public function index()
    {
        $auth = Auth::user();

        $module_ids = ModuleRole::where('role_id','=',$auth->role_id)->where('R','=',true)->pluck('module_id')->toArray();
        $modules = Module::whereIn('id',$module_ids)->pluck('key')->toArray();

        return response(['success' => 1, 'rows' => $modules]);
    }
}