<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BoardController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\MilestoneController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\SprintController;
use App\Http\Controllers\API\PriorityController;
use App\Http\Controllers\API\EffortController;
use App\Http\Controllers\API\UserGroupController;
use App\Http\Controllers\API\RetrospectiveController;
use App\Http\Controllers\API\UserTypeController;
use App\Http\Controllers\API\MenuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login',[AuthController::class,'login']);
Route::post('/signup',[AuthController::class,'signup']);
Route::post('/verify',[AuthController::class,'verify']);
Route::post('/forgot-password', [AuthController::class,'forgotPassword']);
Route::post('/reset-password', [AuthController::class,'resetPassword']);
Route::post('/verify-reset-token', [AuthController::class,'verifyResetPasswordToken']);

Route::group(['middleware' => ['auth:api']], function ($router) {
    Route::get('/logout',[AuthController::class,'logout']);
    Route::get('/check',[AuthController::class,'check']);

    Route::group(['middleware' => ['rolebased']], function ($router) {
        Route::get('/roles',[AuthController::class,'roles']);
    });

    Route::group(['prefix' => 'projects'], function(){
        Route::get('/', [ProjectController::class,'index']);
    });

    Route::get('/modules',[ModuleController::class,'index']);
    Route::post('/modules',[ModuleController::class,'store']);
    Route::get('/modules/find',[ModuleController::class,'getDetailsByKey']);
    Route::get('/modules/user-settings',[ModuleController::class,'getUserSettings']);

    Route::put('/modules/{id}',[ModuleController::class,'update']);
    Route::get('/modules/{id}/roles',[ModuleController::class,'getModuleRoles']);
    Route::put('/modules/{id}/roles',[ModuleController::class,'saveModuleRoles']);
    Route::put('/modules/{id}/boards/sort',[ModuleController::class,'sortModuleBoards']);

    Route::post('/modules/{id}/boards',[ModuleController::class,'createModuleBoard']);
    Route::get('/modules/{id}/boards',[ModuleController::class,'getModuleBoards']);

    Route::post('/modules/{id}/status',[ModuleController::class,'createModuleStatus']);
    Route::get('/modules/{id}/status',[ModuleController::class,'getModuleStatus']);

    Route::get('/roles',[RoleController::class,'index']);
    Route::post('/roles',[RoleController::class,'store']);
    Route::put('/roles/{id}',[RoleController::class,'update']);

    Route::get('/tags',[TagController::class,'index']);
    Route::post('/tags',[TagController::class,'store']);
    Route::put('/tags/{id}',[TagController::class,'update']);

    Route::get('/projects',[ProjectController::class,'index']);
    Route::get('/projects/{id}',[ProjectController::class,'getDetails']);
    Route::get('/projects/task-durations/{id}',[ProjectController::class,'getDuration']);
    Route::post('/projects',[ProjectController::class,'store']);
    Route::put('/projects/',[ProjectController::class,'updateBulk']);
    Route::put('/projects/{id}',[ProjectController::class,'update']);
    Route::delete('/projects',[ProjectController::class,'deleteBulk']);
    Route::delete('/projects/{id}',[ProjectController::class,'delete']);
    Route::put('/projects/{id}/complete',[ProjectController::class,'tagComplete']);
    Route::put('/projects/{id}/uncomplete',[ProjectController::class,'tagUncomplete']);

    Route::get('/priority',[PriorityController::class,'index']);
    Route::post('/priority',[PriorityController::class,'store']);
    Route::put('/priority/{id}',[PriorityController::class,'update']);

    Route::get('/efforts',[EffortController::class,'index']);
    Route::post('/efforts',[EffortController::class,'store']);
    Route::put('/efforts/{id}',[EffortController::class,'update']);

    Route::get('/retrospectives',[RetrospectiveController::class,'index']);
    Route::post('/retrospectives',[RetrospectiveController::class,'store']);
    Route::put('/retrospectives/{id}',[RetrospectiveController::class,'update']);

    Route::get('/tasks',[TaskController::class,'index']);
    Route::put('/tasks-dependencies/{id}',[TaskController::class,'updateDependencies']);
    Route::get('/tasks-chart',[TaskController::class,'chart']);
    Route::post('/tasks',[TaskController::class,'store'])->middleware('rolebased:tasks-create');
    Route::post('/tasks/duplicate',[TaskController::class,'duplicate'])->middleware('rolebased:tasks-create');
    Route::put('/tasks',[TaskController::class,'updateBulk'])->middleware('rolebased:tasks-update');
    Route::put('/tasks/{id}',[TaskController::class,'update'])->middleware('rolebased:tasks-update');
    Route::delete('/tasks',[TaskController::class,'deleteBulk'])->middleware('rolebased:taks-delete');

    // Route::post('/tasks',[TaskController::class,'store']);
    // Route::put('/tasks',[TaskController::class,'updateBulk']);
    // Route::put('/tasks/{id}',[TaskController::class,'update']);
    // Route::delete('/tasks',[TaskController::class,'deleteBulk']);

    Route::get('/boards',[BoardController::class,'index']);
    Route::post('/boards',[BoardController::class,'store']);
    Route::put('/boards/{id}',[BoardController::class,'update']);
    Route::put('/boards/{id}/tasks/sort',[BoardController::class,'sortTasks']);

    Route::get('/milestones',[MilestoneController::class,'index']);
    Route::put('/milestones-dependencies/{id}',[MilestoneController::class,'updateDependencies']);

    Route::get('/milestones-chart',[MilestoneController::class,'chart']);
    Route::post('/milestones',[MilestoneController::class,'store']);
    Route::post('/milestones/duplicate',[MilestoneController::class,'duplicate'])->middleware('rolebased:milestones-create');
    Route::put('/milestones',[MilestoneController::class,'updateBulk'])->middleware('rolebased:milestones-update');
    Route::put('/milestones/{id}',[MilestoneController::class,'update']);
    Route::get('/milestones/duration/{id}',[MilestoneController::class,'getDuration']);
    Route::put('/milestones/{id}/complete',[MilestoneController::class,'tagComplete']);
    Route::put('/milestones/{id}/uncomplete',[MilestoneController::class,'tagUncomplete']);
    Route::delete('/milestones',[MilestoneController::class,'deleteBulk'])->middleware('rolebased:milestones-delete');

    Route::get('/menus',[MenuController::class,'index']);

    Route::get('/departments',[DepartmentController::class,'index']);
    Route::post('/departments',[DepartmentController::class,'store']);
    Route::put('/departments/{id}',[DepartmentController::class,'update']);

    Route::get('/item-types',[ItemTypeController::class,'index']);
    Route::post('/item-types',[ItemTypeController::class,'store']);
    Route::put('/item-types/{id}',[ItemTypeController::class,'update']);

    Route::get('/sprints',[SprintController::class,'index']);
    Route::post('/sprints',[SprintController::class,'store'])->middleware('rolebased:sprints-create');
    Route::post('/sprints/duplicate',[SprintController::class,'duplicate'])->middleware('rolebased:sprints-create');
    Route::put('/sprints',[SprintController::class,'updateBulk'])->middleware('rolebased:sprints-update');
    Route::put('/sprints/{id}',[SprintController::class,'update'])->middleware('rolebased:sprints-update');
    Route::put('/sprints/{id}/complete',[SprintController::class,'tagComplete'])->middleware('rolebased:sprints-update');
    Route::put('/sprints/{id}/uncomplete',[SprintController::class,'tagUncomplete'])->middleware('rolebased:sprints-update');
    Route::delete('/sprints',[SprintController::class,'deleteBulk'])->middleware('rolebased:sprints-delete');

    Route::get('/user-groups',[UserGroupController::class,'index']);
    Route::post('/user-groups',[UserGroupController::class,'store']);
    Route::put('/user-groups/{id}',[UserGroupController::class,'update']);
    Route::get('/user-groups/{id}',[UserGroupController::class,'show']);

    Route::get('/users',[UserController::class,'index']);
    Route::post('/users',[UserController::class,'store'])->middleware('rolebased:users-update');
    Route::put('/users/{id}',[UserController::class,'update'])->middleware('rolebased:users-update');

    Route::get('/status',[StatusController::class,'index']);

    Route::get('/user_type',[UserTypeController::class,'index']);
    Route::post('/user_type',[UserTypeController::class,'store']);
    Route::put('/user_type/{id}',[UserTypeController::class,'update']);
});