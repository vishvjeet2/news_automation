<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\PostController;
use App\Services\AIassistentService;




/*
|------------------------------------------------------------------------
| simple user login rout
| note:- user can't register himself it can only be registerd by 
|------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|----------------------------------------------------------------------------
| Admin Routes
|----------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function (){
    Route::get('/login',[AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login',[AuthController::class,'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class,'logout'])->name('admin.logout');
});

/*
|----------------------------------------------------------------------------
| Protected Admin Routes
|----------------------------------------------------------------------------
*/

Route::prefix('admin')
->middleware('admin.auth', 'no.back.history')
->group(function (){
    Route::get('/dashboard',[AdminDashboardController::class,'index'])->name('admin.dashboard');
    
    Route::get('/dashboard/search',[AdminDashboardController::class,'search'])->name('admin.dashboard.search');
    Route::get('/posts/data', [AdminPostController::class,'datatable']);
    Route::get('/posts/create', [AdminPostController::class,'create'])->name('admin.posts.create');
    Route::post('/posts/{news}/toggle-status', [AdminPostController::class, 'toggleStatus']);

    Route::get('/categories',[CategoryController::class,'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::post('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::post('/posts/generate', [AdminPostController::class, 'store'])->name('admin.post.store');

    Route::get('/template', [AdminPostController::class, 'viewtemplate'])->name('admin.template.index');
    Route::post('/template', [AdminPostController::class, 'addtemplate'])->name('admin.template.store');
    Route::post('/template/{id}', [AdminPostController::class, 'deletetemplate'])->name('admin.template.delete');

    Route::get('/posts/{id}/download', [AdminDashboardController::class, 'download'])->name('admin.post.download');
});





/*
|----------------------------------------------------------------------------
| User Routes
|----------------------------------------------------------------------------
*/

Route::middleware('auth.check:user', 'no.back.history')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

    Route::get('/posts/data', [PostController::class, 'datatable'])->name('posts.data');

    Route::post('/posts/generate', [PostController::class, 'store'])->name('posts.generate');

    Route::patch('/posts/{news}/status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');

    Route::get('/posts/{id}/download', [NewsController::class, 'download'])->name('posts.download');

});


Route::get('/', function () {return view('welcome');});

// Route::get('/test-gemini', [AIassistentService::class, 'testGroq']);
// Route::post('/test-gemini', [AIassistentService::class, 'testGroq']);


    Route::post('/ai/analyze', [AIController::class, 'analyze'])
        ->name('ai.analyze');



    // Route::post('/admin/ai/analyze', [AIController::class, 'analyze'])
    //     ->name('admin.ai.analyze');

