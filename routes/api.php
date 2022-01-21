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
use App\Http\Controllers\StateController;
use App\Http\Controllers\CustomerController;

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
    Route::resource("users",App\Http\Controllers\UserController::class);

    Route::post("logout",[App\Http\Controllers\AuthController::class,'logout']);

    Route::post("register",[App\Http\Controllers\AuthController::class,'register']);

    //para el formulario de agregar nuevo usuario
    Route::post("checkemailUser/{id}",[App\Http\Controllers\AuthController::class,'CheckEmail']);

    Route::resource("optiosapp",OptionAppController::class);

    Route::resource("makes",MakeController::class);

    Route::resource("modelos",ModeloController::class);
    Route::get("modelosbmake/{make}",[App\Http\Controllers\ModeloController::class,'getModeloByMake']);

    Route::resource("trims", TrimController::class);
    Route::get("trimsbmodel/{model}",[App\Http\Controllers\TrimController::class,'getTrimByModelo']);
    Route::get("gettrimfull/{id}",[App\Http\Controllers\TrimController::class,'getTrimFull']);

    Route::resource("companies", CompanyController::class);

    Route::resource("branches", BranchController::class);

    Route::resource("styles", StyleController::class);

    Route::resource("cars", CarController::class);
    Route::post("checkvincar/{id}",[App\Http\Controllers\CarController::class,'CheckVIN']);
    Route::post("checksnumbercar/{id}",[App\Http\Controllers\CarController::class,'CheckSckNumber']);
    Route::get("getCarTable",[App\Http\Controllers\CarController::class,'getCarTable']);
    Route::get("getFullCar/{id}",[App\Http\Controllers\CarController::class,'getFullCar']);

    Route::resource("states", StateController::class);

    Route::resource("customers", CustomerController::class);
    Route::post("checkdriverlCust/{id}",[App\Http\Controllers\CustomerController::class,'CheckDriverL']);
    Route::post("checkemailCust/{id}",[App\Http\Controllers\CustomerController::class,'CheckEmail']);
    Route::post("checkdbirthCust",[App\Http\Controllers\CustomerController::class,'CheckDateBirth']);
    Route::post("checkssnCust/{id}",[App\Http\Controllers\CustomerController::class,'CheckSSN']);


});


//Public Routes
Route::post("login",[App\Http\Controllers\AuthController::class,'login']);
Route::post("reset_password",[App\Http\Controllers\AuthController::class,'reset_password']);
Route::post("save_password",[App\Http\Controllers\AuthController::class,'save_password']);
