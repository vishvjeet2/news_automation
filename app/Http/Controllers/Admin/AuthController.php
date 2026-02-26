<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Admin\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $result = $this->authService->register($data);

        return response()->json([
            'status' => true,
            'message' => 'Admin Registered Successfully',
            'admin' => $result['admin'],
            'token' => $result['token']
        ]);
    }

    public function login(LoginRequest $request){

        $credentials = $request->validated();

        $token = $this->authService->login($credentials);

        return redirect()->route('admin.dashboard')->cookie('admin_jwt', $token, 60*24, '/', null, true, true);
    }

 public function me()
    {
        return response()->json([
            'admin' => $this->authService->me()
        ]);
    }
public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}
