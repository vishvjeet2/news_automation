<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{


    public function index(){
        $categories=Category::latest()->pagination();
        return view('admin.categories.index',compact('categories'));
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
            'name' => 'required|string|unique:categories,name',
            'slug' => 'required|string|unique:categories,slug',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return back()->with('success', 'Category added successfully');
    }
}