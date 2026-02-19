<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CobaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RolePositionController;
use App\Http\Controllers\Api\UserController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (token required)
Route::middleware('auth:sanctum')->group(function () {
    //users
    Route::apiResource('users', UserController::class);
    //province
    Route::prefix('provinces')->group(function () {
        Route::get('/', [ProvinceController::class, 'index'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR');
        Route::get('/{id}', [ProvinceController::class, 'show'])
            ->middleware('allow:STAF,ADMINKOPERASI');
        Route::post('/', [ProvinceController::class, 'store'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR1');

        Route::put('/{id}', [ProvinceController::class, 'update'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR1');

        Route::delete('/{id}', [ProvinceController::class, 'destroy'])
            ->middleware('allow:ADMINISTRATOR');
    });

    Route::prefix('news')->group(function () {
        Route::get('/export-news', [NewsController::class, 'export']);
        Route::get('/', [NewsController::class, 'index'])
            ->middleware('allow:ADMINISTRATOR1,ADMINISTRATOR1');
        Route::get('/by-province-id/{id}', [NewsController::class, 'getByProvinceId'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR1');
        Route::get('/{id}', [NewsController::class, 'show'])
            ->middleware('allow:STAF,ADMINKOPERASI');

        Route::post('/', [NewsController::class, 'store'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR1');

        Route::put('/{id}', [NewsController::class, 'update'])
            ->middleware('allow:ADMINISTRATOR,ADMINISTRATOR1');

        Route::delete('/{id}', [NewsController::class, 'destroy'])
            ->middleware('allow:ADMINISTRATOR');
    });
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('role-positions', RolePositionController::class);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/refersh', [AuthController::class, 'me']);
});

Route::get('/test', function () {
    return response()->json(['ok' => true]);
});

Route::get('getecryp', [RoleController::class, 'testDecrypt']);
