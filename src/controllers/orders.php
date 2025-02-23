<?php

class OrdersController
{
    private $session;
    private $database;
    private $booking;
    private $item;
    private $order;

    public function __construct(Session $session, Database $database) {
        $this->session = $session;
        $this->database = $database;
        $this->booking = new Booking($this->database, $this->session);
        $this->item = new Item($this->database);
        $this->order = new Order($this->database, $this->session);
    }

    public function handleRequest() {
        require_once "include/utils.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT, "bookings/orders");
        }

        $bookings = $this->booking->getBookings($this->session->username);
        $items = $this->item->getItems();
        $orders = $this->order->getOrders($this->session->username);
        $orders_and_items = [];
        if (! isset($_REQUEST["error"])) {
            if (isset($bookings->error)) {
                redirect("bookings", $bookings->message, $bookings->message);
            }
            if (isset($items->error)) {
                redirect("bookings", $items->message, $items->message);
            }
            if (isset($orders->error)) {
                redirect("bookings", $orders->message, $orders->message);
            }
        }
        if (isset($orders->result)) {
            foreach ($orders->result as $order) {
                $orders_and_items[$order["order_id"]] = $this->order->getOrderItems($this->session->username, $order["order_id"]);
            }
        }

        $booking_id = $_POST["booking"] ?? FALSE;
        $items_and_quantities =  self::sortItems($items);
        $submit_button_pressed = $_POST["order_items"] ?? FALSE;

        if ($submit_button_pressed) {
            $order = new Order($this->database, $this->session);
            $ordered_items = $order->orderItems($booking_id, $items_and_quantities);
            if (isset($ordered_items->error)) {
                redirect("bookings/orders", $ordered_items->message, $ordered_items->message);
            }
            redirect("bookings/orders", $ordered_items->message);
        }

        require "views/orders.php";
        exit();
    }

    private function sortItems($items) {
        $items_and_quantities = [];
        foreach ($items->result as $item) {
            $item_quantity = $_POST["quantity_of_" . $item["item_name"]] ?? NULL;
            $items_and_quantities[$item["item_name"]] = $item_quantity;
        }

        return $items_and_quantities;
    }
}
