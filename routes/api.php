<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakeController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\TrimController;
use App\Http\Controllers\OptionAppController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StyleController;
use App\Http\Controllers\CarController;

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



    Route::resource("optiosapp",OptionAppController::class);

    Route::resource("makes",MakeController::class);

    Route::resource("modelos",ModeloController::class);
    Route::get("modelosbmake/{make}",[App\Http\Controllers\ModeloController::class,'getModeloByMake']);

    Route::resource("trims", TrimController::class);
    Route::get("trimsbmodel/{model}",[App\Http\Controllers\TrimController::class,'getTrimByModelo']);

    Route::resource("companies", CompanyController::class);

    Route::resource("branches", BranchController::class);

    Route::resource("styles", StyleController::class);

    Route::resource("cars", CarController::class);
    Route::post("checkvincar/{id}",[App\Http\Controllers\CarController::class,'ChechVIN']);
    Route::post("checksnumbercar/{id}",[App\Http\Controllers\CarController::class,'ChechSckNumber']);

});


//Public Routes

Route::post("login",[App\Http\Controllers\AuthController::class,'login']);
Route::post("register",[App\Http\Controllers\AuthController::class,'register']);


