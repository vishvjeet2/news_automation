<?php

use illuminate\support\facades\Route;
use App\Http\Controllers\Admin\AuthController ;

Route::prefix('admin')->group( function(){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);

    Route::middleware('auth:admin')->group(function () {

        Route::get('/me',[AuthController::class,'me']);
        Route::post('/logout',[AuthController::class,'logout']);

    });
});

