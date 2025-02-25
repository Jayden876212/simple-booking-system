<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Session;

class RegistrationController extends Controller
{
    private const MIN_USERNAME_LENGTH = 1;
    private const MAX_USERNAME_LENGTH = 20;

    private const MIN_PASSWORD_LENGTH = 1;
    private const MAX_PASSWORD_LENGTH = 18;

    public function show(): View
    {
        return view("pages/register", [
            "MIN_USERNAME_LENGTH" => self::MIN_USERNAME_LENGTH,
            "MAX_USERNAME_LENGTH" => self::MAX_USERNAME_LENGTH,
            "MIN_PASSWORD_LENGTH" => self::MIN_PASSWORD_LENGTH,
            "MAX_PASSWORD_LENGTH" => self::MAX_PASSWORD_LENGTH
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            "username" => ["required", "unique:users,username", "min:".self::MIN_USERNAME_LENGTH, "max:".self::MAX_USERNAME_LENGTH],
            "password" => ["required", "min:".self::MIN_PASSWORD_LENGTH, "max:".self::MAX_PASSWORD_LENGTH]
        ]);

        User::create([
            "username" => $request->username,
            "password" => Hash::make($request->password)
        ]);

        return redirect('/account/login')->with('success', 'Registration successful! Please log in.');
    }
}