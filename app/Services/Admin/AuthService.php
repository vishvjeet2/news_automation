<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = Auth::guard('admin')->login($admin);

        return [
            'admin' => $admin,
            'token' => $token
        ];
    }

    public function login(array $data)
    {
        $token = Auth::guard('admin')->attempt($data);
        
        if (!$token) {
            return back()->with('error', 'Invalid credentials');
        }

        return $token;
    }

    public function me()
    {
        return Auth::guard('admin')->user();
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return true;
    }

}
