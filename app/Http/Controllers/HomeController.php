<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(Request $request): View
    {
        return view("pages.home")->with("page_title", "Home");
    }    

    public function handleRedirect(Request $request) {
        $success = $request->session()->get("success") ?? false;
        $error = $request->session()->get("error") ?? false;
        $has_error_or_success = $error or $success;
        $new_route = redirect("/");
        if ($has_error_or_success) {
            $new_route = $new_route->with(
                $success ? "success" : "error", // Get key
                $success ?: $error // Get message
            );
        }

        return $new_route;
    }
}