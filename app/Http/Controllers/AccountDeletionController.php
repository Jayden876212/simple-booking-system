<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Services\AccountDeletionService;

class AccountDeletionController extends Controller
{
    protected $auth;
    protected $accountDeletionService;

    public function __construct(Guard $auth, AccountDeletionService $accountDeletionService)
    {
        $this->auth = $auth;
        $this->accountDeletionService = $accountDeletionService;
    }

    public function delete(Request $request): RedirectResponse
    {
        $user_authenticated = $this->auth->check();
        if (! $user_authenticated) {
            return redirect()->route("home")->with("error", "Account deletion failed. (You are not logged in.)");
        }

        $user = $this->auth->user();
        $deleted_account = $this->accountDeletionService->deleteAccount($user);
        if (! $deleted_account) {
            return redirect()->route("home")->with("error", "Account deletion failed due to server error.");
        }

        $request->session()->invalidate();
        return redirect()->route("home")->with("success", "Successfully deleted account!");
    }
}
