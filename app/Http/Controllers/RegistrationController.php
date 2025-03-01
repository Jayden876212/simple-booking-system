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

    public function __construct(User $user, Hash $hash)
    {
        $this->user = $user;
        $this->hash = $hash;
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

        $registrationService = new RegistrationService($this->user, $this->hash);
        $registrationService->register($request->username, $request->password);

        return redirect()->route("login.show")->with("success", "Registration successful! Please log in.");
    }
}
