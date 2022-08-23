<?php

use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Auth\PermissionController;
use App\Http\Controllers\Api\v1\Car\CarController;
use App\Http\Controllers\Api\v1\Car\CarServiceController;
use App\Http\Controllers\Api\v1\Wallet\WalletController;
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

        Route::prefix('auth')->middleware(['role:admin'])->group(function(){
            Route::prefix('roles')->group(function (){
                Route::post('create',[PermissionController::class,'createRole']);
                Route::post('givePermission',[PermissionController::class,'givePermissionToRole']);
            });
            Route::prefix('permissions')->group(function (){
                Route::post('create',[PermissionController::class,'createPermission']);
            });
            Route::prefix('users')->group(function (){
                Route::post('createUser',[AuthController::class,'createUser']);
                Route::get( 'permissions/{user_id}', [PermissionController::class, 'getPermissionByUser']);
                Route::post('giveRole',[PermissionController::class,'giveRoleToUser']);
                Route::post('givePermission', [PermissionController::class, 'givePermissionToUser']);
            });
        });

        Route::prefix('wallet')->middleware(['role:user'])->group(function(){
            Route::post('addBalance',[WalletController::class,'addBalance']);
            Route::get('balance',[WalletController::class,'getBalance']);
            Route::get('balance/history',[WalletController::class,'getBalanceHistory']);
            Route::post('payout',[WalletController::class,'payout']);
        });

        Route::prefix('user')->middleware(['role:user'])->group(function(){
            Route::get('/',[AuthController::class,'getUser']);
        });

        Route::prefix('services')->middleware(['role:user'])->group(function(){
            Route::get('/',[CarServiceController::class,'getServices']);
        });

        Route::post('logout',[AuthController::class,'logout']);
    });
});
