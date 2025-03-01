<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RegistrationService;
use Hash;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    protected $user;
    protected $hash;
    protected $registrationService;

    public function __construct(User $user, Hash $hash, RegistrationService $registrationService)
    {
        $this->user = $user;
        $this->hash = $hash;
        $this->registrationService = $registrationService;
    }

    public function showRegister(): View
    {
        return view("pages.register", ["page_title" => "Register"]);
    }

    public function register(Request $request)
    {
        $request->validate([
            "username" => ["required", "unique:users,username", "min:".config("constants.MIN_USERNAME_LENGTH"), "max:".config("constants.MAX_USERNAME_LENGTH")],
            "password" => ["required", "min:".config("constants.MIN_PASSWORD_LENGTH"), "max:".config("constants.MAX_PASSWORD_LENGTH")]
        ]);

        $registered_successfully = $this->registrationService->register($request->username, $request->password);
        if (! $registered_successfully) {
            return redirect()->route("register.show")->with("error", "Registration failed due to server error.");
        }

        return redirect()->route("login.show")->with("success", "Registration successful! Please log in.");
    }
}
