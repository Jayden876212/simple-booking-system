<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class RegistrationController extends Controller
{
    protected $user;
    protected $hash;
    protected $registrationService;

    public function __construct(User $user, Hash $hash)
    {
        $this->user = $user;
        $this->hash = $hash;
    }

    public function showRegister(): View
    {
        return view("pages.register", ["page_title" => "Register"]);
    }

    public function register(RegistrationRequest $request): RedirectResponse
    {
        try {
            $this->user->create([
                "username" => $request->username,
                "password" => $this->hash::make($request->password)
            ]);
        } catch (Throwable $throwable) {
            return redirect()->route("register.show")->with("error", "Registration failed due to server error.");
        }

        return redirect()->route("login.show")->with("success", "Registration successful! Please log in.");
    }
}
