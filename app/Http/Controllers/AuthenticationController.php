<?php

namespace App\Http\Controllers;

use App\Services\AuthenticationService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticationController extends Controller
{
    protected $auth;
    protected $authenticationService;

    public function __construct(Guard $auth, AuthenticationService $authenticationService) {
        $this->auth = $auth;
        $this->authenticationService = $authenticationService;
    }

    public function handleRedirect(): RedirectResponse {
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

    public function login(Request $request): RedirectResponse
    {
        if ($this->auth->check()) {
            return redirect()->route("home")->with('error', 'Error - account already logged in.');
        }

        $request->validate([
            "username" => ["required"],
            "password" => ["required"]
        ]);
        $authenticated_successfully = $this->authenticationService->authenticate($request["username"], $request["password"]);
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
        $request->session()->invalidate();

        return redirect()->route("home")->with("success", "Logout successfull!");
    }
}
