<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function handle() {
       if (Auth::check()) {
            return redirect("/home");
        } else {
            return redirect("/account/register");
        }
    }
}