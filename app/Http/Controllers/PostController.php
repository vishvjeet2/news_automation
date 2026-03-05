<?php

namespace App\Http\Controllers;
use App\Services\NewsGeneratorService;
use App\Models\News;
use App\Models\Category;
use App\Models\Template;
use Illuminate\Http\Request;

class PostController extends Controller
{

    protected $newsService;

    public function __construct(NewsGeneratorService $newsService)
    {
        $this->newsService = $newsService;
    }

    /*
|--------------------------------------------------------------------------
| Generate Post (User)
|--------------------------------------------------------------------------
|
| This method acts as the entry point for user post generation.
|
| Why this exists here instead of a separate NewsController:
| - Generation belongs to the post workflow.
| - We removed NewsController to simplify architecture.
| - Controller remains thin and delegates business logic to service.
|
| What happens here:
| - Accepts user request
| - Delegates full generation process to NewsGeneratorService
| - Service handles:
|     - Validation
|     - Image/Video/Text generation
|     - Ownership assignment (user_id)
|     - File storage
|     - Database persistence
|
*/

public function store(Request $request)
{
    return $this->newsService->generate($request);
}



    /* 
    |--------------------------------------------------------------------------
    | Show Create Post Form
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------
    | Loads categories and templates for the user create form.
    | No generation logic here.
    |--------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::all();
        $templates  = Template::all();

        return view('news.news', compact('categories', 'templates'));
    }

    /*
    |--------------------------------------------------------------------------
    | Download Latest Generated Output
    |--------------------------------------------------------------------------
    |
    | Ensures user can only download their own posts.
    |
    */

    public function download($id)
    {
        $news = News::with('latestOutput')->findOrFail($id);

        // Ownership check
        if ($news->user_id !== session('user_id')) {
            abort(403, 'Unauthorized access');
        }

        if (!$news->latestOutput) {
            abort(404, 'No generated output found');
        }

        $filePath = storage_path(
            'app/public/' . $news->latestOutput->file_path
        );

        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        return response()->download($filePath);
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Post Status (Temporary For User)
    |--------------------------------------------------------------------------
    |
    | Currently allowed for user.
    | Later this can be restricted to admin only.
    |
    */

    public function toggleStatus(News $news)
    {
        // Ownership protection
        if ($news->user_id !== session('user_id')) {
            abort(403, 'Unauthorized access');
        }

        $news->status = $news->status === 'processed'
            ? 'draft'
            : 'processed';

        $news->save();

        return response()->json([
            'status' => $news->status,
            'label'  => ucfirst($news->status),
        ]);
    }
}
