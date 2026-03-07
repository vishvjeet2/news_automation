<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsOutput;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $stats = [
            'total' => News::where('user_id', $userId)->count(),
            'images' => News::where('user_id', $userId)->where('news_type','image')->count(),
            'videos' => News::where('user_id', $userId)->where('news_type','video')->count(),
            'drafts' => News::where('user_id', $userId)->where('status','draft')->count(),
        ];

        $posts = News::with(['category','latestOutput'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard', compact('stats','posts'));
    }



    public function search(Request $request)
    {
        $search = $request->search;
        $posts = News::with(['category','latestOutput'])
        ->where('user_id', session('user_id'))
        ->latest()
        ->get();

        return view('_getnews', compact('posts'))->render();
    }
}