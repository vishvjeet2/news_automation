<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('admin_jwt');

        if (!$token) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
