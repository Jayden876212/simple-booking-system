<?php

namespace App\Models;

use Carbon\Traits\ToStringFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

use Exception;
use DB;

enum OrderError: string {
    case BOOKING_ID_EMPTY = "You must select a booking ID.";
    case AT_LEAST_ONE_ITEM = "You must purchase at least one item.";
    case BOOKING_ID_NOT_EXIST = "A booking with that ID does not exist.";
    case ITEM_NAMES_NOT_EXIST = "An item with that name does not exist.";
    case USER_NO_PERMISSION = "The selected booking belongs to another user (booking username must be the same as the one in the session).";
    case ITEM_QUANTITY_ZERO_OR_LESS = "The quantity of the selected items must be at least 1.";
    case BOOKING_NOT_IN_TIMESLOT = "Your order must be done at the time between your timeslot's start time and the next timeslot's start time.";
}

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        "booking_id",
        "datetime_ordered"
    ];
    public $timestamps = false;

    private static function createRows($items_and_quantities, $last_insert_id) {
        $rows = [];
        foreach ($items_and_quantities as $item_name => $item_quantity) {
            $rows[] = [
                "item_name" => $item_name,
                "order_id" => $last_insert_id,
                "quantity" => $item_quantity
            ];
        }

        return $rows;
    }

    public static function orderItems($booking_id, $items_and_quantities) {
        $items_are_found = false;
        if (isset($items_and_quantities)) {
            foreach ($items_and_quantities as $name => $quantity) {
                if ($name AND $quantity) {
                    $items_are_found = true;
                }
            }
        }

        $items_exist = true;
        $quantity_less_than_zero = false;
        if ($items_are_found) {
            foreach ($items_and_quantities as $name => $quantity) {
                $item = Item::getItem($name);

                if (! $item) {
                    $items_exist = false;
                    break;
                }
            }

            $items_to_be_removed = [];
            foreach ($items_and_quantities as $name => $quantity) {
                if ($quantity < 0) {
                    $quantity_less_than_zero = true;
                } else if ($quantity == 0) {
                    $items_to_be_removed[] = $name;
                }
            }
            foreach ($items_to_be_removed as $item_name) {
                unset($items_and_quantities[$item_name]);
            }
        }

        $ordered_timeslots = Timeslot::getOrderedTimeslots()->toArray();
        $timeslot_start_times = [];
        foreach ($ordered_timeslots as $timeslot) {
            $timeslot_start_times[] = $timeslot["timeslot_start_time"];
        }

        $booking = Booking::getBooking($booking_id);
        $start_time_key = array_search($booking["timeslot_start_time"], $timeslot_start_times);
       
        $valid_start_time = $ordered_timeslots[$start_time_key]["timeslot_start_time"];
        $valid_end_time = $ordered_timeslots[$start_time_key + 1]["timeslot_start_time"];

        $valid_start_datetime = $booking["booking_date"] . " " . $ordered_timeslots[$start_time_key]["timeslot_start_time"];
        $valid_end_datetime = $booking["booking_date"] . " " . $ordered_timeslots[$start_time_key + 1]["timeslot_start_time"];

        $valid_start_datetime_unix = strtotime($valid_start_datetime);
        $valid_end_datetime_unix = strtotime($valid_end_datetime);

        $error = match(true) {
            ($booking_id == NULL) OR ($booking_id == 0) OR ($booking_id == "") => OrderError::BOOKING_ID_EMPTY,
            ! $items_are_found => OrderError::AT_LEAST_ONE_ITEM,
            ! $booking => OrderError::BOOKING_ID_NOT_EXIST,
            ! $items_exist => OrderError::ITEM_NAMES_NOT_EXIST,
            $booking["username"] != Auth::user()["username"] => OrderError::USER_NO_PERMISSION,
            $quantity_less_than_zero => OrderError::ITEM_QUANTITY_ZERO_OR_LESS,
            // (time() < $valid_start_datetime_unix) OR (time() >= $valid_end_datetime_unix) => OrderError::BOOKING_NOT_IN_TIMESLOT,
            default => false
        };

        if ($error) {
            throw new Exception($error->value, 1);
        }


        $created_order = Order::create([
            "booking_id" => $booking_id,
            "datetime_ordered" => DB::raw("NOW()")
        ]);
        $last_insert_id = $created_order->id;
        $rows = self::createRows($items_and_quantities, $last_insert_id);
        ItemOrder::insert($rows);
    }

    public static function getOrders($user_id) {
        $orders = User::find($user_id)->orders()->toBase()
        ->join("item_orders", "orders.id", "=", "item_orders.order_id")
        ->join("items", "items.name", "=", "item_orders.item_name")
        ->selectRaw("orders.id, MAX(orders.datetime_ordered) AS datetime_ordered, SUM(item_orders.quantity * items.price) AS total_price")
        ->groupBy("orders.id")
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