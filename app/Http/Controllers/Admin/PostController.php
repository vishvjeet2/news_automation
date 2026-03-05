<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Template;
use App\Models\News;
use Illuminate\Http\Request;
use App\Services\NewsGeneratorService;

class PostController extends Controller
{
    protected $newsService;

    public function __construct(NewsGeneratorService $newsService)
    {
        $this->newsService = $newsService;
    }

    /*
    |--------------------------------------------------------------------------
    | Show Create Post Form (Admin)
    |--------------------------------------------------------------------------
    |
    | Loads categories and templates for form.
    | No business logic here.
    |
    */

    public function create()
    {
        $categories = Category::all();
        $templates  = Template::all();

        return view('admin.posts.create', compact('categories', 'templates'));
    }

    /*
|--------------------------------------------------------------------------
| Generate Post (Admin)
|--------------------------------------------------------------------------
|
| This method is the admin entry point for post generation.
|
| Important:
| - Admin and User both use the SAME NewsGeneratorService.
| - No duplicate generation logic exists in admin layer.
| - Service automatically assigns ownership using session:
|       - If admin_id exists → assigns admin_id
|       - Otherwise assigns user_id
|
| Why this is important:
| - Prevents logic duplication
| - Keeps controllers thin
| - Keeps business logic centralized
| - Safe for production
|
*/

    public function store(Request $request)
    {
        return $this->newsService->generate($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Download Latest Generated Output
    |--------------------------------------------------------------------------
    */

    public function download($id)
    {
        $news = News::with('latestOutput')->findOrFail($id);

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
    | Toggle Post Status (Admin Only)
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(News $news)
    {
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
