<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Show Category Management Page
    |--------------------------------------------------------------------------
    |
    | Loads paginated categories.
    | Used for admin management.
    |
    */

    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }


    /*
    |--------------------------------------------------------------------------
    | Store New Category (Admin Only)
    |--------------------------------------------------------------------------
    |
    | This method:
    | - Validates input
    | - Ensures name and slug are unique
    | - Creates new category
    | - Returns appropriate response
    |
    | Improvements made:
    | - Used unique validation instead of manual exists() check
    | - Removed redundant query
    | - Simplified logic
    | - Used Laravel validation features properly
    |
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'required|string|max:100|unique:categories,slug',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category added susccesfully.');
    }
}
