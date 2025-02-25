<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Session;

class LoginController extends Controller
{
    public function show(): View
    {
        return view("pages/login");
    }

    public function login(Request $request): RedirectResponse
    {
        return redirect('/home')->with('success', 'Login successful!');
    }
}