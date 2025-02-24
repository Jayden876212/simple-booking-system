<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::redirect("/home", "/");