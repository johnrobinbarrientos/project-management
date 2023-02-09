<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Auth;

use App\Models\ModuleRole;
use App\Models\Module;

class RoleBasedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $params = null)
    {
        $actions = ['create' => 'C', 'read' => 'R', 'update' => 'U', 'delete' => 'D'];
        $user = Auth::user();
        $params = explode('-',$params);
        
        $module = $params[0];
        $module = ucwords($module);
        $module = Module::where('name','=',$module)->first();

        
        $action = $params[1];
        $action = $actions[$action];

        if ($module) {

            $module_role = ModuleRole::where('module_id','=',$module->id)->where('role_id','=',$user->role_id)->where($action,'=',true)->first();

            if (!$module_role) {
                return response()->json(['success' => 0, 'user' => $user, 'authenticated' => true, 'message' => 'Unable to perform action'], 403);
            }
        }

    
        return $next($request);
    }
}



