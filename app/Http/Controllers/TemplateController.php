<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function store(Request $request)
    {
        $data = json_encode($request->elements);

        // Template::create([
        //     'layout' => $data
        // ]);

        return response()->json(['success' => true]);
    }
}
