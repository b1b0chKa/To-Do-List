<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;





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

//USER
Route::group([
	'prefix' 		=> 'user',
],
	function()
	{
		Route::post('/registration', [UserController::class, 'register']);
		Route::post('/login', [UserController::class, 'login']);
		Route::get('/{user_id}', [UserController::class, 'profile']);
	}
);

Route::group([
	'prefix' 		=> 'user',
	'middleware' 	=> 'auth:sanctum'
],
function()
{
	Route::patch('/update/{user_id}', [UserController::class, 'updateProfile']);
	Route::delete('/delete/{name}', [UserController::class, 'deleteProfile']);
	Route::delete('/logout', [UserController::class, 'logout']);
});

//TASK
Route::group([
	'prefix' 		=> 'task',
	'middleware' 	=> 'auth:sanctum'
],
	function()
	{
		Route::get('/all', [TaskController::class, 'getAll']);
		Route::get('/{task_id}', [TaskController::class, 'getById']);
		Route::post('/create', [TaskController::class, 'create']);
		Route::patch('/update/{task_id}', [TaskController::class, 'update']);
		Route::delete('/delete/{task_id}', [TaskController::class, 'delete']);
	}
);
//TAGS
Route::group([
	'prefix' 		=> 'tag',
	'middleware' 	=> 'auth:sanctum'
],
	function()
	{
		Route::get('/all', [TagController::class, 'getAll']);
		Route::post('/create', [TagController::class, 'create']);
		Route::patch('/update/{tag_id}', [TagController::class, 'update']);
		Route::delete('/delete/{tag_id}', [TagController::class, 'delete']);
	}
);

//CATEGORY
Route::group([
	'prefix' 		=> 'category',
	'middleware' 	=> 'auth:sanctum'
],
	function()
	{
		Route::get('/all', [CategoryController::class, 'getAll']);
		Route::post('/create', [CategoryController::class, 'create']);
		Route::patch('/update/{category_id}', [CategoryController::class, 'update']);
		Route::delete('/delete/{category_id}', [CategoryController::class, 'delete']);
	}
);
