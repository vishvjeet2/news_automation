<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Template;
use App\Models\News;
use Illuminate\Http\Request;
use App\Services\NewsGeneratorService;
use Illuminate\Support\Facades\DB;

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

    public function viewtemplate(){
        $templates  = Template::paginate(5);

        return view('Admin.categories.template', compact('templates'));
    }

    public function addtemplate(Request $request){

        $request->validate([
            'name'          => 'required|string|max:255|unique:templates,name',
            'template_path' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

         // Handle image upload
        $imageName = strtolower(str_replace(' ', '_', $request->name)) . '.jpeg';
        $request->file('template_path')->move(storage_path('app/public/templates'), $imageName);

        Template::create([
            'name'          => $request->name,
            'template_path' => 'templates/' . $imageName,
        ]);

        return redirect()->back()->with('success', 'Template added successfully.');
    }



    public function deletetemplate($id)
    {
        $template = Template::findOrFail($id);

        // Delete the image file from storage
        $filePath = storage_path('app/public/' . $template->template_path);
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $template->delete();

        return redirect()->back()->with('success', 'Template deleted successfully.');
    }



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

    public function datatable(Request $request)
    {

        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;

        $query = News::with(['admin','user']);

        $totalRecords = $query->count();

        $posts = $query->skip($start)
                    ->take($length)
                    ->latest()
                    ->get();

        $data = [];

        foreach($posts as $post){

            $statusClass = ($post->status ?? 'draft') === 'processed'
            ? 'bg-green-100 text-green-800 border-green-300'
            : 'bg-yellow-100 text-yellow-800 border-yellow-300';

            $data[] = [

                'heading' => $post->heading,

                'news_type' => $post->news_type ?? 'N/A',

                'category' => $post->category ?? '-',

                'status' => '
                    <button 
                    id="status-btn-'.$post->id.'"
                    onclick="toggleStatus('.$post->id.')" 
                    class="px-2.5 py-1 rounded-full text-xs font-medium border '.$statusClass.'">
                        '.ucfirst($post->status ?? 'draft').'
                    </button>
                    ',

                'created_by' => $post->admin->name ?? $post->user->name ?? 'Unknown',

                'date' => $post->created_at->format('d M Y'),

                'action' => '<a href="'.route('admin.post.download',$post->id).'" 
                class="text-blue-600 font-bold hover:underline">Preview</a>'

            ];

        }

        return response()->json([
            "draw"=>intval($draw),
            "recordsTotal"=>$totalRecords,
            "recordsFiltered"=>$totalRecords,
            "data"=>$data
        ]);

    }
}
