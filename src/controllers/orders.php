<?php

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

            $submit_button_pressed = $_POST["order_items"] ?? FALSE;

            if (isset($booking_id) AND $submit_button_pressed) {
                $items_and_quantities = [];
                foreach ($items->result as $item) {
                    $item_quantity = $_POST["quantity_of_" . $item["item_name"]] ?? 0;
                    if ($item_quantity > 0) {
                        $items_and_quantities[$item["item_name"]] = $item_quantity;
                    }
                }

                if ($items_and_quantities) {
                    $order = new Order($this->database, $this->session);
                    $ordered_items = $order->orderItems($booking_id, $items_and_quantities);
                    if (isset($ordered_items->error)) {
                        redirect("bookings/orders", $ordered_items->message, $ordered_items->message);
                    }
                    redirect("bookings/orders", $ordered_items->message);
                }
            }
        }

        require "views/orders.php";
        exit();
    }
}
