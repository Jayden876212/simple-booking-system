<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class AccountDeletionController extends Controller
{
    protected $auth;
    protected $user;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->user = User::find($auth->id());
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        $user_authenticated = $this->auth->check();
        if (! $user_authenticated) {
            return redirect()->route("home")->with("error", "Account deletion failed. (You are not logged in.)");
        }

        try {
            $this->user->delete();
        } catch (Throwable $caught) {
            return redirect()->route("home")->with("error", "Account deletion failed due to server error.");
        }

        $request->session()->invalidate();
        return redirect()->route("home")->with("success", "Successfully deleted account!");
    }
}
