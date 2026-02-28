<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsOutput;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $posts = News::with(['category', 'latestOutput'])
            ->where('user_id', session('user_id'))
            ->latest()
            ->paginate(10);
        
        

        $stats = [
            'total' => News::where('user_id', $userId)->count(),
            'images' => NewsOutput::where('user_id', $userId)->where('output_type','image')->count(),
            'videos' => NewsOutput::where('user_id', $userId)->where('output_type','video')->count(),
            'drafts' => News::where('user_id', $userId)->where('status','draft')->count(),
        ];

        return view('dashboard', compact('posts','stats'));
    }
}