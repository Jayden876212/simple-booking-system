<?php

class BookingsController
{
    private $session;
    private $account;
    private $database;
    private $booking;

    public function __construct(Session $session, Account $account, Database $database) {
        $this->session = $session;
        $this->account = $account;
        $this->database = $database;
        $this->booking = new Booking($this->database, $this->session, $this->account);
    }

    public function handleRequest() {
        require_once "include/utils.php";
        require_once "models/booking.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings");
        }

        $timeslots = (new Timeslot($this->database))->getTimeslots();

        if (isset($timeslots->error)) {
            redirect("bookings", CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }

        $create_booking = $_POST["create_booking"] ?? FALSE;
        $timeslot_start_time = $_POST["timeslot_start_time"] ?? FALSE;
        $booking_date = $_POST["booking_date"] ?? FALSE;
        $form_submitted = $create_booking AND $booking_date AND $timeslot_start_time;
        if ($form_submitted) {
            $created_booking = $this->booking->createBooking($timeslot_start_time, $booking_date);
            if (! $created_booking->error) {
                redirect("bookings", $created_booking->message);
            } else {
                redirect("bookings", $created_booking->message, CrudOperation::IS_ERROR);
            }
        }

        require "views/bookings.php";
        exit();
    }
}

class TimeslotController
{
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }
}