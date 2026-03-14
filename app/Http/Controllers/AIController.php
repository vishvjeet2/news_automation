<?php

namespace App\Http\Controllers;

use App\Services\AIassistentService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIassistentService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'raw_content' => 'required|string',
            'template_type' => 'required'
        ]);

        return $this->aiService->analyze($request);
    }
}
