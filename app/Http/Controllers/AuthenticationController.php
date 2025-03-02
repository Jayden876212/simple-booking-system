<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticationController extends Controller
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handleRedirect(): RedirectResponse
    {
        if ($this->auth->check()) {
            return redirect()->route("home");
        } else {
            return redirect()->route("register.show");
        }
    }

    public function showLogin(): View|RedirectResponse
    {
        if ($this->auth->check()) {
            return redirect()->route("home")->with('error', 'Error - account already logged in.');
        }

        return view("pages.login", ["page_title" => "Login"]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->auth->check()) {
            return redirect()->route("home")->with('error', 'Error - account already logged in.');
        }

        $credentials = [
            "username" => $request["username"],
            "password" => $request["password"]
        ];

        $authenticated_successfully = $this->auth->attempt($credentials);

        if (! $authenticated_successfully) {
            return back()->withErrors([
                "password" => "Credentials do not match"
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route("home")->with('success', 'Login successful!');
    }

    public function logout(Request $request)
    {
        if (! $this->auth->check()) {
            return redirect()->route("home")->with("error", "Logout failed. (You are not logged in.)");
        }
        $this->auth->logout();

        return redirect()->route("home")->with("success", "Logout successfull!");
    }
}
