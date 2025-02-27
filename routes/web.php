<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::get("/home", function () {
    return redirect("/");
});

// Account Pages
Route::get("/account", [AccountController::class, "handleRedirect"]);

Route::get("/account/register", [AccountController::class, "showRegister"]);
Route::post("/account/register", [AccountController::class, "register"]);

Route::get("/account/login", [AccountController::class, "showLogin"]);
Route::post("/account/login", [AccountController::class, "login"]);

Route::get("/account/logout", [AccountController::class, "logout"]);