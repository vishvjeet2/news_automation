<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function showadminlogin(){
        return view('Admin.AdminLogin');
    }

    public function admin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            session([
                'user_id' => $admin->id,
                'user_name' => $admin->name
            ]);

            return redirect('/admindashboard');
        }

        return back()->with('error', 'Invalid Credentials');
    }
}
