<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\LoginController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::get("/home", function () {
    return redirect("/");
});

// Account Pages
Route::get("/account", [AccountController::class, "handle"]);

Route::get("/account/register", [RegistrationController::class, "show"]);
Route::post("/account/register", [RegistrationController::class, "register"]);

Route::get("/account/login", [LoginController::class, "show"]);
Route::post("/account/login", [LoginController::class, "login"]);