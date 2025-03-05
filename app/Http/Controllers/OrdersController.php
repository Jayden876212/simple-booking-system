<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Purchase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Item;
use Throwable;

class OrdersController extends Controller
{
    protected $auth;
    protected $user;

    protected $bookings;
    protected $items;
    protected $orders;
    protected $purchases;

    public function __construct(Guard $auth, User $users, Booking $bookings, Item $items, Order $orders, Purchase $purchases)
    {
        // Get the existing instance of the logged in user
        $this->auth = $auth;
        $this->user = $users::find($auth->id());


        // Initialise model dependencies
        $this->bookings = $bookings;
        $this->items = $items;
        $this->orders = $orders;
        $this->purchases = $purchases;
    }

    private static function sanitiseUnselectedItems(array $items): array {
        $items_to_be_removed = [];
        foreach ($items as $name => $quantity) {
            if ($quantity == 0) {
                $items_to_be_removed[] = $name;
            }
        }
        foreach ($items_to_be_removed as $item_name) {
            unset($items[$item_name]);
        }

        return $items;
    }

    public function showOrders(Request $request): RedirectResponse|View {
        if (! Auth::check()) {
            return redirect()->route("login.show")->with("error", "You must be logged in to an account to make an order.");
        }

        // If the user gets redirected to the page with the booking_id argument in the URL,
        // we may wish to automatically select the booking in the select element.
        $booking_id = $request->booking_id ?? FALSE;

        $bookings = $this->bookings->getBookings($this->user);
        $items = $this->items->getItems();
        $orders = $this->orders->getOrders($this->user, $this->items, $this->purchases);

        $orders_and_items = [];
        if (isset($orders)) {
            foreach ($orders as $order) {
                $order_instance = $this->orders->find($order->id);
                $orders_and_items[$order->id] = $this->purchases->getPurchases(
                    $this->user,
                    $order_instance,
                    $this->orders,
                    $this->items
                )->toArray();
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

    public function makeOrder(OrderRequest $request): RedirectResponse {
        // Get the booking from the user and find it among the bookings in the database
        $booking_id = $request->booking;

        try {
            $booking = $this->bookings->getBooking($booking_id);
        } catch (Throwable $caught) {
            return redirect()->route("orders.show")->with("error", "Error in order - failed to retrieve booking from database.");
        }

        // Trim away items sent in the form request that are of quantity 0
        $items = self::sanitiseUnselectedItems($request["items"]);

        try {
            $this->orders->makeOrder($booking, $items, $this->purchases);
            return redirect()->route("orders.show")->with("success", "Successfully ordered items.");
        } catch (Throwable $caught) {
            return redirect()->route("orders.show")->with("error", "Failed to order items.");
        }
    }
}