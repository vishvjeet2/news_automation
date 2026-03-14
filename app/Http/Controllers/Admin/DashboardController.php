<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\NewsOutput;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $posts = News::with(['category','admin','user','latestOutput'])
            ->latest()
            ->get();

        // Dashboard stats
        $stats = [
            'total'     => News::count(),
            'published' => News::where('status', 'processed')->count(),
            'drafts'    => News::where('status', 'draft')->count(),
            'users'     => User::count(),
        ];

        return view('admin.dashboard', compact('stats', 'posts'));
    }



    /*
    |--------------------------------------------------------------------------
    | AJAX: Load posts for DataTable
    |--------------------------------------------------------------------------
    */

    // public function search(Request $request)
    // {

    //     $posts = News::with(['category','admin','user','latestOutput'])
    //         ->latest()
    //         ->get();

    //     return view('Admin._getnewsadmin', compact('posts'))->render();
    // }



    /*
    |--------------------------------------------------------------------------
    | Download / Preview Media
    |--------------------------------------------------------------------------
    */

    public function download($id)
    {

        $media = NewsOutput::where('news_id', $id)->first();

        if (!$media) {
            abort(404, 'Media not found in DB');
        }

        $relativePath = $media->file_path;

        $fullPath = storage_path('app/public/' . $relativePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File missing on server');
        }

        $publicPath = 'storage/' . $relativePath;

        return view('news.adminpreview', [
            'image' => $publicPath
        ]);
    }

}