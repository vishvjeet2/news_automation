<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\News\NewsController;



Route::get('/newstype', [NewsController::class, 'fetch_data']);

Route::post('/newstype', [NewsController::class, 'newstype'])->name('news.create');














////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function(){
    Route::get('/login', function(){
        return view('AdminLogin');
    });

    Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', fn () => view('Admin.AdminDashboard'))->name('admin.dashboard');
    });
});
