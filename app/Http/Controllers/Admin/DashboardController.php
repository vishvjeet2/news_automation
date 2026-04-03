<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\News;
use App\Models\User;
use App\Models\NewsOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmailJob;

class DashboardController extends Controller
{

    public function index()
    {
        $posts = News::with(['category','admin','user','latestOutput'])
            ->latest()
            ->get();

        // Dashboard stats
        $stats = [
            'total'     => News::count(),
            'published' => News::where('status', 'processed')->count(),
            'drafts'    => News::where('status', 'draft')->count(),
            'users'     => User::count(),
        ];

        return view('admin.dashboard', compact('stats', 'posts'));
    }



    /*
    |--------------------------------------------------------------------------
    | AJAX: Load posts for DataTable
    |--------------------------------------------------------------------------
    */

    // public function search(Request $request)
    // {

    //     $posts = News::with(['category','admin','user','latestOutput'])
    //         ->latest()
    //         ->get();

    //     return view('Admin._getnewsadmin', compact('posts'))->render();
    // }



    /*
    |--------------------------------------------------------------------------
    | Download / Preview Media
    |--------------------------------------------------------------------------
    */

    public function download($id)
    {

        $media = NewsOutput::where('news_id', $id)->first();

        if (!$media) {
            abort(404, 'Media not found in DB');
        }

        $relativePath = $media->file_path;

        $fullPath = storage_path('app/public/' . $relativePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File missing on server');
        }

        $publicPath = 'storage/' . $relativePath;

        return view('news.adminpreview', [
            'image' => $publicPath
        ]);
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'role'     => 'required'
        ]);
    
        // store plain password for email
        $plainPassword = $request->password;

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ];
    
        /*
        |--------------------------------------------------------------------------
        | Store Based on Role
        |--------------------------------------------------------------------------
        */
    
        if ($request->role === 'admin') {

            if (Admin::where('email', $request->email)->exists()) {
                return back()
                    ->withErrors(['email' => 'Admin with this email already exists'])
                    ->withInput();
            }
    
            $user = Admin::create($data);

            // push job to queue
            SendEmailJob::dispatch($user);
    
        } else {
    
            if (User::where('email', $request->email)->exists()) {
                return back()
                    ->withErrors(['email' => 'User with this email already exists'])
                    ->withInput();
            }
    
            $user = User::create($data);
            // push job to queue
            SendEmailJob::dispatch($user);
        }
    
        return back()->with('success', 'User created successfully');
    }

    public function viewUser(){

        return view('Admin.adduser');
    }

}