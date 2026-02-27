<?php
// i have removed the authcontroller currently to reduce confusion
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\News\NewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;




// simple user login rout
// note:- user can't register himself it can only be registerd by 

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


//////////////////////////////////////////////

// user routs

Route::middleware('auth.check')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/posts/create', [NewsController::class, 'fetch_data'])->name('posts.create');

    Route::post('/posts/generate', [NewsController::class, 'newstype'])->name('posts.generate');

    Route::get('/posts/{id}/download', [PostController::class, 'download'])->name('posts.download');

    Route::get('/categories', function () {return view('news.catagory'); })->name('view.category.store');

    Route::post('/categories', [NewsController::class, 'insertnewscatogarytype'])->name('category.store');

    Route::get('/templates', function () { return view('news.templet');})->name('templates.index');

});






// Route::get('/templates', function(){
//     return view('news.templet');
// });

Route::post('/templates', [NewsController::class, 'storetemplete'])->name('news.templates.store');



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
