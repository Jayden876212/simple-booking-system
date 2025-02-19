<?php

class BookingsController {
    private $session;
    private $account;
    private $database;

    public function __construct(Session $session, Account $account, Database $database) {
        $this->session = $session;
        $this->account = $account;
        $this->database = $database;
    }

    public function handleRequest() {
        require_once "include/utils.php";
        require_once "models/booking.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings");
        }

        $timeslots = (new Timeslot($this->database))->getTimeslots();

        require "views/bookings.php";
        exit();
    }
}