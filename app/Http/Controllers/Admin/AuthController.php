<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(){
        return view('admin.login');
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $admin=Admin::where('email',$request->email)->first();
        if(!$admin ||!Hash::check($request->password,$admin->password)){
            return back()->with('error','Invalid credentials');
        }

       session([
        'admin_id'=>$admin->id,
        "admin_name"=>$admin->name
       ]);

       return redirect()->route('admin.dashboard');

    }

     public function logout()
    {
        session()->forget(['admin_id', 'admin_name']);

        return redirect()->route('admin.login');
    }
}
