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

    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }

    public function handleRedirect(): RedirectResponse {
        if ($this->auth->check()) {
            return redirect()->route("home");
        } else {
            return redirect()->route("register.show");
        }
    }

    public function showLogin(): View
    {
        return view("pages.login", ["page_title" => "Login"]);
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            "username" => ["required"],
            "password" => ["required"]
        ]);
        $authenticationService = new AuthenticationService($this->auth);
        $authenticated_successfully = $authenticationService->authenticate($request["username"], $request["password"]);
        if ($authenticated_successfully) {
            $request->session()->regenerate();

            return redirect()->route("home")->with('success', 'Login successful!');
        }

        return back()->withErrors([
            "password" => "Credentials do not match"
        ]);
    }

    public function logout(Request $request)
    {
        if ($this->auth->check()) {
            $request->session()->invalidate();

            return redirect()->route("home")->with("success", "Logout successfull!");
        } else {
            return redirect()->route("home")->with("error", "Logout failed. (You are not logged in.)");
        }
    }
}
