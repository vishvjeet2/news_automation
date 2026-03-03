<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;




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
->middleware('admin.auth')
->group(function (){
    Route::get('/dashboard',[AdminDashboardController::class,'index'])->name('admin.dashboard');
    Route::get('/posts/create', [AdminPostController::class,'create'])->name('admin.posts.create');
    Route::post('/posts/{news}/toggle-status', [AdminPostController::class, 'toggleStatus']);
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories', [AdminPostController::class, 'index'])->name('admin.categories.index');
    Route::post('/posts/generate', [AdminPostController::class, 'store'])->name('admin.post.store');
});





/*
|----------------------------------------------------------------------------
| User Routes
|----------------------------------------------------------------------------
*/

Route::middleware('auth.check:user')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

    Route::post('/posts/generate', [PostController::class, 'newstype'])->name('posts.generate');

    Route::patch('/posts/{news}/status', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');

    Route::get('/posts/{id}/download', [PostController::class, 'download'])->name('posts.download');

});






// Route::get('/templates', function(){
//     return view('news.templet');
// });

//Route::post('/templates', [NewsController::class, 'storetemplete'])->name('news.templates.store');



// Route::get('/news', function(){
//     return view('news.news1');
// });












////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('/', function () {
    return view('welcome');
});


// Route::group(['prefix' => 'admin'], function(){
//     Route::get('/login', function(){
//         return view('AdminLogin');
//     });

//    // Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

//     Route::middleware('admin.auth')->group(function () {
//         Route::get('/dashboard', fn () => view('Admin.AdminDashboard'))->name('admin.dashboard');
//     });
// });
