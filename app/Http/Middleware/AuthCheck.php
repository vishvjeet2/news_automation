<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\User;

class AuthCheck
{
    public function handle($request, Closure $next, $role = null)
    {
        // ❌ Not logged in
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        $userId = session('user_id');

        // ✅ Detect role automatically
        if (Admin::where('id', $userId)->exists()) {
            $currentRole = 'admin';
        } elseif (User::where('id', $userId)->exists()) {
            $currentRole = 'user';
        } else {
            session()->flush();
            return redirect('/login');
        }

        // ✅ If specific role required
        if ($role && $role !== $currentRole) {
            abort(403, 'Unauthorized access');
        }

        // store role for later use (optional)
        session(['role' => $currentRole]);

        return $next($request);
    }
}