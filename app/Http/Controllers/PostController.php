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

    public function toggleStatus(News $news)
    {
        // handle NULL values safely
        // dd($news->id);
        $currentStatus = $news->status ?? 'draft';

        // toggle status
        $news->status = $currentStatus === 'processed' ? 'draft' : 'processed';

        $news->save();   // ✅ updates news table

        return response()->json([
            'status' => $news->status,
            'label' => ucfirst($news->status),
        ]);
    }
}