<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    private const MIN_USERNAME_LENGTH = 20;
    private const MIN_PASSOWORD_LENGTH = 20;
    private const MAX_USERNAME_LENGTH = 20;
    private const MAX_PASSWORD_LENGTH = 18;

    public function show(): View
    {
        return view("pages/register");
    }

    public function register(Request $request)
    {
        $request->validate([
            "username" => ["required", "unique:users"],
            "password" => ["required"]
        ]);

        User::create([
            "username" => $request->username,
            "password" => Hash::make($request->password)
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please log in.');
    }
}