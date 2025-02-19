<?php

class BookingsController {
    private $session;
    private $account;

    public function __construct(Session $session, Account $account) {
        $this->session = $session;
        $this->account = $account;
    }

    public function handleRequest() {
        require_once "include/utils.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings");
        }

        require "views/bookings.php";
        exit();
    }
}