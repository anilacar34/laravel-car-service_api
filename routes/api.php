<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
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

Route::prefix('v1')->group(function (){
    Route::post('login',[AuthController::class,'login'])->middleware("throttle:10,1");

    Route::group(['middleware' => ['auth:sanctum']],function(){
        Route::post('createUser',[AuthController::class,'createUser']);
        Route::post('logout',[AuthController::class,'logout']);
    });
});


