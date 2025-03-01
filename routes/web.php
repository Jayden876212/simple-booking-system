<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AccountDeletionController;

// Home/Landing Page
Route::get("/", [HomeController::class, "show"]);
Route::get("/home", [HomeController::class, "handleRedirect"])->name("home");

// Account Pages
Route::get("/account", [AuthenticationController::class, "handleRedirect"])->name("accounts");

Route::get("/account/register", [RegistrationController::class, "showRegister"])->name("register.show");
Route::post("/account/register", [RegistrationController::class, "register"])->name("register.handle");

Route::get("/account/login", [AuthenticationController::class, "showLogin"])->name("login.show");
Route::post("/account/login", [AuthenticationController::class, "login"])->name("login.handle");

Route::get("/account/logout", [AuthenticationController::class, "logout"])->name("logout");
Route::get("/account/delete", [AccountDeletionController::class, "delete"])->name("account.delete");

// Bookings Pages
Route::get("/bookings", [BookingsController::class, "showBookings"])->name("bookings.show");
Route::post("/bookings", [BookingsController::class, "makeBooking"])->name("bookings.handle");
Route::get("/bookings/get-unavailable-timeslots", [TimeslotController::class, "handleRequest"])->name("get-unavailable-timeslots");
Route::get("/bookings/cancel", [BookingsController::class, "cancelBooking"])->name("bookings.cancel");

// Orders Pages
Route::get("/bookings/orders", [OrdersController::class, "showOrders"])->name("orders.show");
Route::post("/bookings/orders", [OrdersController::class, "makeOrder"])->name("orders.handle");