<?php

namespace App\Models;

use Carbon\Traits\ToStringFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

use Exception;
use DB;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        "booking_id",
        "datetime_ordered"
    ];
    public $timestamps = false;

    public static function makeOrder(Booking $booking) {
        $created_order = Order::create([
            "booking_id" => $booking->id,
            "datetime_ordered" => DB::raw("NOW()")
        ]);

        return $created_order;
    }

    private static function createRows($items, Order $order) {
        $item_orders = [];
        foreach ($items as $item_name => $item_quantity) {
            $item_orders[] = [
                "item_name" => $item_name,
                "order_id" => $order->id,
                "quantity" => $item_quantity
            ];
        }
        $created_item_orders = ItemOrder::insert($item_orders);

        return $created_item_orders;
    }

    private static function removeUnselectedItems($items) {
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

    public static function orderItems($booking_id, $items) {
        $items = self::removeUnselectedItems($items);

        $booking = Booking::getBooking($booking_id);

        $created_order = self::makeOrder($booking);
        self::createRows($items, $created_order);
    }

    public function getOrders($user_id, Item $item, ItemOrder $itemOrder) {
        $items = $item->getTable();
        $item_orders = $itemOrder->getTable();
        $orders_table = $this->getTable();

        $orders = User::find($user_id)->orders()->toBase()
        ->join($item_orders, "$orders_table.id", "=", "$item_orders.order_id")
        ->join($items, "$items.name", "=", "$item_orders.item_name")
        ->selectRaw(
            "$orders_table.id,
            MAX($orders_table.datetime_ordered) AS datetime_ordered,
            SUM($item_orders.quantity * $items.price) AS total_price"
        )->groupBy("$orders_table.id")
        ->get();

        return $orders;
    }

    public function getOrderItems($user_id, $order_id, Item $item, ItemOrder $itemOrder) {
        $items = $item->getTable();
        $item_orders = $itemOrder->getTable();
        $orders = $this->getTable();

        $order = User::find($user_id)->orders()->toBase()
        ->where("$orders.id", "=", $order_id)
        ->join($item_orders, "$orders.id", "=", "$item_orders.order_id")
        ->join($items, "$items.name", "=", "$item_orders.item_name")
        ->selectRaw(
            "$item_orders.item_name,
            SUM($item_orders.quantity) AS quantity,
            SUM($items.price) AS price,
            SUM($item_orders.quantity * $items.price) AS total_price"
        )->groupBy("$item_orders.item_name")
        ->get();

        return $order;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, "item_orders");
    }
}