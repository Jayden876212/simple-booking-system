<?php

require_once "include/utils.php";
require_once "models/booking.php";

require_once "controllers/accounts.php";
require_once "controllers/home.php";
require_once "controllers/bookings.php";

const HOST = "http://localhost";
const WORKING_DIRECTORY = "/simple-booking-system/src";
$original_request = $_SERVER["REQUEST_URI"];
$request = str_replace(WORKING_DIRECTORY, "", $original_request);

switch (strtok($request, "?")) {
    case "/":
        $controller = new HomeController($session, $account);
        $controller->handleRequest();
        break;
    case "/home":
        $controller = new HomeController($session, $account);
        $controller->handleRequest();
        break;
    case "/account/login":
        $controller = new LoginController($session, $account);
        $controller->handleRequest();
        break;
    case "/account/register":
        $controller = new RegistrationController($session, $account);
        $controller->handleRequest();
        break;
    case "/account/logout":
        $controller = new LogoutController($session, $account);
        $controller->handleRequest();
        break;
    case "/account/delete":
        $controller = new AccountDeletionController($session, $account);
        $controller->handleRequest();
        break;
    case "/bookings":
        $controller = new BookingsController($session, $account, $database);
        $controller->handleRequest();
        break;
    case "/bookings/get-unavailable-timeslots":
        $controller = new TimeslotController($database, $session, $account);
        $controller->handleRequest();
        break;
    default:
        echo $request;
        echo "404 not found";
        break;
}