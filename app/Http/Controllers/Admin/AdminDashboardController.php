<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsOutput;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
        public function index()
    {
        // ✅ Get all posts (no user filter)
        $posts = News::with(['category', 'latestOutput'])
            ->latest()
            ->paginate(10);

        // ✅ Global stats
        $stats = [
            'total'  => News::count(),
            'images' => NewsOutput::where('output_type', 'image')->count(),
            'videos' => NewsOutput::where('output_type', 'video')->count(),
            'drafts' => News::where('status', 'draft')->count(),
        ];

        return view('Admin.AdminDashboard', compact('posts', 'stats'));
    }
}
