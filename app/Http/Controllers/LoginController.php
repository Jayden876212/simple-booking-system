<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Session;

class LoginController extends Controller
{
    public function show(): View
    {
        return view("pages/login", ["page_title" => "Login"]);
    }

    public function login(Request $request)
    {
        $request->validate([
            "username" => ["required"],
            "password" => ["required"]
        ]);
        $credentials = [
            "username" => $request["username"],
            "password" => $request["password"]
        ];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended("/home")->with('success', 'Login successful!');
        }

        return back()->withErrors([
            "password" => "Credentials do not match"
        ]);
    }
}