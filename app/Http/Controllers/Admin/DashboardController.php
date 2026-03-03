<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;

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
            'published' => News::where('status', 'publish')->count(),
            'drafts'    => News::where('status', 'draft')->count(),
            'users'     => User::count(),
        ];

        return view('admin.dashboard', compact('posts', 'stats'));
    }
}