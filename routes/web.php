<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::redirect("/home", "/");

// Account Pages
Route::get("/account/register", [RegistrationController::class, "show"]);
Route::post("/account/register", [RegistrationController::class, "register"]);