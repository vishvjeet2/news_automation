<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\User;

class AuthCheck
{
    
    public function handle($request, Closure $next)
    {
        // Not logged in
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');

        // If user does not exist
        if (!User::where('id', $userId)->exists()) {
            session()->flush();
            return redirect('/login');
        }

        return $next($request);
    }
}