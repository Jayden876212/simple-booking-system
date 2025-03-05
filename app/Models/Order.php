<?php

namespace App\Models;

use Carbon\Traits\ToStringFormat;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

use Exception;
use DB;
use Throwable;

class Order extends Model
{
    protected $table = "orders";
    protected $fillable = [
        "booking_id",
        "datetime_ordered"
    ];
    public $timestamps = false;

    // ------------------

    public static function makeOrder(Booking $booking, array $chosen_items, Purchase $purchases) 
    {
        $order = self::insertOrderForBooking($booking);
        
        if ($order) {
            try {
                $purchases::makePurchases($chosen_items, $order);
            } catch (Throwable $caught) {
                $order->delete();
                throw $caught;
            }
        }

        return [$order, $purchases];
    }

    private static function insertOrderForBooking(Booking $booking): mixed
    {
        $created_order = self::create([
            "booking_id" => $booking->id,
            "datetime_ordered" => DB::raw("NOW()")
        ]);

        return $created_order;
    }

    // ------------------

    public function getOrders(User $user, Item $items, Purchase $purchases): Collection {
        $items_table = $items->getTable();
        $purchases_table = $purchases->getTable();
        $orders_table = $this->getTable();

        $orders = $user->orders()->toBase()
        ->join($purchases_table, "$orders_table.id", "=", "$purchases_table.order_id")
        ->join($items_table, "$items_table.name", "=", "$purchases_table.item_name")
        ->selectRaw(
            "$orders_table.id,
            MAX($orders_table.datetime_ordered) AS datetime_ordered,
            SUM($purchases_table.quantity * $items_table.price) AS total_price"
        )->groupBy("$orders_table.id")
        ->get();

        return $orders;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, "item_orders");
    }
}