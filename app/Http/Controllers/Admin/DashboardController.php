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
        $search = request('search');


        $posts = News::with(['user', 'admin', 'category', 'latestOutput'])
            ->when($search, function ($query) use ($search) {
                $query->where('heading', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(4)
            ->withQueryString();

        $stats = [
            'total'  => News::count(),
            'images' => News::whereHas('latestOutput', fn($q) => $q->where('output_type','image'))->count(),
            'videos' => News::whereHas('latestOutput', fn($q) => $q->where('output_type','video'))->count(),
            'drafts' => News::where('status','draft')->count(),
        ];

        return view('admin.dashboard', compact('posts','stats'));
    }



    // Ajaxcall

    public function search(Request $request)
    {
        $search = $request->search;

        $posts = News::with(['category','latestOutput'])
            ->where('heading', 'like', "%{$search}%")
            ->latest()
            ->get();

        return view('_getnews', compact('posts'))->render();
    }
    
    public function download($id)
    {
        $media = NewsOutput::where('news_id', $id)->first();

        if (!$media) {
            abort(404, 'Media not found in DB');
        }

        // file stored inside storage/app/public/
        $relativePath = $media->file_path;

        $fullPath = storage_path('app/public/' . $relativePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File missing on server');
        }

        // create public URL for preview
        $publicPath = 'storage/' . $relativePath;

        return view('news.adminpreview', [
            'image' => $publicPath
        ]);
    }
}