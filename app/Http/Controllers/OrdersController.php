<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Item;

class OrdersController extends Controller
{
    protected $auth;
    protected $user;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->user = $auth->user();
    }

    public function showOrders(Request $request) {
        if (! Auth::check()) {
            return redirect("account/login")->with("error", "You must be logged in to an account to make an order.");
        }

        $booking_id = $request->booking_id ?? FALSE;

        $bookings = Booking::getBookings($this->user);
        $items = Item::getItems();
        $orders = Order::getOrders(Auth::id());

        $orders_and_items = [];
        if (isset($orders)) {
            foreach ($orders as $order) {
                $order = (array) $order;
                $orders_and_items[$order["id"]] = Order::getOrderItems(Auth::id(), $order["id"]);
            }
        }

        return view("pages.orders", [
            "booking_id" => $booking_id,
            "bookings" => $bookings,
            "items" => $items,
            "orders" => $orders->toArray(),
            "orders_and_items" => $orders_and_items
        ])->with("page_title", "Orders");
    }

    public function makeOrder(Request $request) {

        $items = Item::getItems();

        $booking_id = $request->booking ?? FALSE;
        $items_and_quantities =  self::sortItems($request, $items);
        $submit_button_pressed = $request->order_items ?? FALSE;

        if ($submit_button_pressed) {
            Order::orderItems($booking_id, $items_and_quantities);
            return redirect("/bookings/orders")->with("success", "Successfully ordered items.");
        }

        require "views/orders.php";
        exit();
    }

    private function sortItems(Request $request, $items) {
        $input = $request->all();
        $items_and_quantities = [];
        foreach ($items as $item) {
            $item_quantity = $input["quantity_of_" . $item["name"]] ?? NULL;
            $items_and_quantities[$item["name"]] = $item_quantity;
        }

        return $items_and_quantities;
    }
}