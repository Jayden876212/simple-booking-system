<?php

class BookingsController
{
    private $session;
    private $database;
    private $booking;

    public function __construct(Session $session, Database $database) {
        $this->session = $session;
        $this->database = $database;
        $this->booking = new Booking($this->database, $this->session);
    }

    public function handleRequest() {
        require_once "include/utils.php";
        require_once "models/booking.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings");
        }

        if (! isset($_REQUEST["error"])) {
            $timeslots = (new Timeslot($this->database))->getTimeslots();
            if (isset($timeslots->error)) {
                redirect("bookings", CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }

            $bookings = $this->booking->getBookings($this->session->username);
            if (isset($bookings->error)) {
                redirect("bookings", CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }

            $unavailable_timeslots = $this->booking->getUnavailableTimeslots(date("Y-m-d"));
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
        }

        require "views/bookings.php";
        exit();
    }
}

class TimeslotController
{
    private $database;
    private $session;

    public function __construct(Database $database, Session $session) {
        $this->database = $database;
        $this->session = $session;
    }

    public function handleRequest() {
        $chosen_booking_date = $_REQUEST["booking_date"] ?? FALSE;
        if ($chosen_booking_date) {
            $booking = new Booking($this->database, $this->session);
            $unavailable_timeslots = $booking->getUnavailableTimeslots($chosen_booking_date);
            if (! isset($unavailable_timeslots->error) && isset($unavailable_timeslots->result)) {
                $unavailable_timeslots_processed = array_column($unavailable_timeslots->result, "number_of_tables_booked", "timeslot_start_time");
                $unavailable_timeslots_json =  json_encode($unavailable_timeslots_processed);
                echo $unavailable_timeslots_json;
            }
        }
        exit;
    }
}

class OrdersController
{
    private $session;
    private $database;
    private $booking;
    private $item;

    public function __construct(Session $session, Database $database) {
        $this->session = $session;
        $this->database = $database;
        $this->booking = new Booking($this->database, $this->session);
        $this->item = new Item($this->database);
    }

    public function handleRequest() {
        require_once "include/utils.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings/orders");
        }

        if (! isset($_REQUEST["error"])) {
            $bookings = $this->booking->getBookings($this->session->username);
            if (isset($bookings->error)) {
                redirect("bookings/orders", $bookings->message, $bookings->message);
            }
            $items = $this->item->getItems();
            if (isset($items->error)) {
                redirect("bookings/orders", $items->message, $items->message);
            }

            if (isset($_POST["booking"])) {
                $booking_id = $_POST["booking"];
            }

            $items_and_quantities = [];
            foreach ($items->result as $item) {
                $item_quantity = $_POST["quantity_of_" . $item["item_name"]] ?? 0;
                if ($item_quantity > 0) {
                    $items_and_quantities[$item["item_name"]] = $item_quantity;
                }
            }
            if ($items_and_quantities and isset($booking_id)) {
                $order = new Order($this->database, $this->session);
                $ordered_items = $order->orderItems($booking_id, $items_and_quantities);
                if (isset($ordered_items->error)) {
                    redirect("bookings/orders", $ordered_items->message, $ordered_items->message);
                }
                redirect("bookings/orders", $ordered_items->message);
            }
        }

        require "views/orders.php";
        exit();
    }
}

class BookingCancellationController
{
    private $session;
    private $database;
    private $booking;

    public function __construct(Session $session, Database $database) {
        $this->session = $session;
        $this->database = $database;
        $this->booking = new Booking($this->database, $this->session);
    }

    public function handleRequest() {
    }
}