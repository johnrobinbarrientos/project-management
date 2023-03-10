<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::group(['prefix' => 'projects'], function(){
//     Route::get('/', 'API\ProjectController@index');
// });
Route::get('', 'App\Http\Controllers\SpaController@index')->where('any', '.*');
Route::get('/tester', 'App\Http\Controllers\SpaController@tester');
Route::get('/{any}', 'App\Http\Controllers\SpaController@index')->where('any', '.*');

