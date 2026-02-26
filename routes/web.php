<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\News\NewsController;

// Route::post('/createnewsimage', [NewsController::class, 'generateNewswithImage']);

// Route::post('/createnewstext', [NewsController::class, 'generateNewstext']);

// Route::post('/createnewsvideo', [NewsController::class, 'generateNewsvideo']);

Route::get('/tts', [NewsController::class, 'hindiTTS']);

Route::get('/newstype', [NewsController::class, 'fetch_data']);

Route::post('/newstype', [NewsController::class, 'newstype'])->name('news.create');




// Route::get('/newstyp', function(){
//     return view('randomimage');
// });





// main automation routs
Route::get('/testimage', [NewsController::class, 'createimage']);

Route::get('/testvideo', [NewsController::class, 'createvideo']);

Route::get('/newsimage', [NewsController::class, 'createImagenews']);

Route::get('/newspdf', [NewsController::class, 'generateNewsImage']);




/* GET route to show form */
Route::get('/category', function () {
    return view('news.test');
});


/* POST route to handle form */
Route::post('/category', [NewsController::class, 'insertnewstype'])->name('category.store');


Route::get('/templates', function(){
    return view('news.templet');
});

Route::post('/templates', [NewsController::class, 'storetemplete'])->name('news.templates.store');



// Route::get('/news', function(){
//     return view('news.news1');
// });












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
