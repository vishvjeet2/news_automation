<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function download($id)
    {
        $news = News::with('latestOutput')->findOrFail($id);

        if (!$news->latestOutput) {
            abort(404);
        }

        return response()->download(
            storage_path('app/public/' . $news->latestOutput->file_path)
        );
    }
}