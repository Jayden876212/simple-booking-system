<?php

require_once "include/utils.php";
require_once "controllers/accounts.php";
require_once "controllers/home.php";

// $working_directory = "/Notes/Knowledge/College/T%20Level%20in%20Digital%20Production,%20Design,%20and%20Development/Year%202%20-%20Occupational%20Specialism/6%20-%20Creating%20a%20Solution/Projects/Simple%20Booking%20System/src";
// $original_request = $_SERVER["REQUEST_URI"];
// $request = str_replace($working_directory, "", $original_request);

$request = $_SERVER["REQUEST_URI"];

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
    default:
        echo "404 not found";
        break;
}