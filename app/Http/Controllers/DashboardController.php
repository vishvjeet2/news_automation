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
            'images' => NewsOutput::where('user_id', $userId)->where('output_type','image')->count(),
            'videos' => NewsOutput::where('user_id', $userId)->where('output_type','video')->count(),
            'drafts' => News::where('user_id', $userId)->where('status','draft')->count(),
        ];

        return view('dashboard', compact('stats'));
    }


    public function search(Request $request)
    {
        $search = $request->search;
        $posts = News::with(['category','latestOutput'])
            ->where('heading', 'like', "%{$search}%")
            ->latest()
            ->paginate(4);

        return view('_getnews', compact('posts'))->render();
    }
}