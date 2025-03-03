<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Item;
use App\Models\ItemOrder;

class OrdersController extends Controller
{
    protected $auth;
    protected $user;

    protected $order;
    protected $item;
    protected $itemOrder;
    protected $booking;

    public function __construct(Guard $auth, Order $order, Item $item, ItemOrder $itemOrder, Booking $booking)
    {
        $this->auth = $auth;
        $this->user = User::find($auth->id());

        $this->order = $order;
        $this->item = $item;
        $this->itemOrder = $itemOrder;

        $this->booking = $booking;
    }

    public function showOrders(Request $request) {
        if (! Auth::check()) {
            return redirect()->route("login.show")->with("error", "You must be logged in to an account to make an order.");
        }

        $booking_id = $request->booking_id ?? FALSE;

        $bookings = $this->booking->getBookings($this->user);
        $items = $this->item->getItems();
        $orders = $this->order->getOrders(Auth::id(), $this->item, $this->itemOrder);

        $orders_and_items = [];
        if (isset($orders)) {
            foreach ($orders as $order) {
                $order = (array) $order;
                $orders_and_items[$order["id"]] = $this->order->getOrderItems(
                    Auth::id(),
                    $order["id"],
                    $this->item,
                    $this->itemOrder
                );
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

        $items = $this->item->getItems();

        $booking_id = $request->booking ?? FALSE;
        $items_and_quantities =  self::sortItems($request, $items);
        $submit_button_pressed = $request->order_items ?? FALSE;

        if ($submit_button_pressed) {
            $this->order->orderItems($booking_id, $items_and_quantities);
            return redirect()->route("orders.show")->with("success", "Successfully ordered items.");
        }
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