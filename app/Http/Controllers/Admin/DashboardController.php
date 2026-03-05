<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\NewsOutput;

class DashboardController extends Controller
{
    public function index()
    {
        // Latest posts from all users (no user filter)
        $posts = News::with(['user', 'admin', 'category', 'latestOutput'])
            ->latest()
            ->paginate(10);

        // System level stats
        $stats = [
            'total'     => News::count(),
            'published' => News::where('status', 'processed')->count(),
            'drafts'    => News::where('status', 'draft')->count(),
            'users'     => User::count(),
        ];

        return view('admin.dashboard', compact('posts', 'stats'));
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