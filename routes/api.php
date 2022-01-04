<?php

use Illuminate\Http\Request;
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
//Route::middleware('auth:sanctum')->get('/user', function () {
//
//});

//Protected Routes
Route::group(['middleware'=>['auth:sanctum']],function()
{
    Route::get("usersget/{user}",[App\Http\Controllers\UserController::class,'show']);
    Route::post("logout",[App\Http\Controllers\AuthController::class,'logout']);
    Route::get("usersget",[App\Http\Controllers\UserController::class,'index']);
    Route::post("register",[App\Http\Controllers\AuthController::class,'regiter']);
});


//Public Routes

Route::post("login",[App\Http\Controllers\AuthController::class,'login']);
