<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\TimeslotController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::get("/home", [HomeController::class, "handleRedirect"]);

// Account Pages
Route::get("/account", [AccountController::class, "handleRedirect"]);

Route::get("/account/register", [AccountController::class, "showRegister"]);
Route::post("/account/register", [AccountController::class, "register"]);

Route::get("/account/login", [AccountController::class, "showLogin"]);
Route::post("/account/login", [AccountController::class, "login"]);

Route::get("/account/logout", [AccountController::class, "logout"]);
Route::get("/account/delete", [AccountController::class, "delete"]);

// Bookings Pages
Route::get("/bookings", [BookingsController::class, "showBookings"]);
Route::post("/bookings", [BookingsController::class, "makeBooking"]);
Route::get("/bookings/get-unavailable-timeslots", [TimeslotController::class, "handleRequest"]);