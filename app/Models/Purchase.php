<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Purchase extends Model
{
    protected $table = "item_orders";
    protected $fillable = [
        "id",
        "item_name",
        "order_id",
        "quantity"
    ];

    public $timestamps = false;

    public static function makePurchases(array $chosen_items, Order $order): bool
    {
        $purchases = [];
        foreach ($chosen_items as $item_name => $item_quantity) {
            $purchases[] = [
                "item_name" => $item_name,
                "order_id" => $order->id,
                "quantity" => $item_quantity
            ];
        }
        $purchased_items = self::insert($purchases);

        return $purchased_items;
    }

    public function getPurchases(User $user, Order|Collection $order, Order $orders, Item $items): Collection {
        $items_table = $items->getTable();
        $orders_table = $orders->getTable();
        $purchases_table = $this->getTable();

        $purchases = $user->orders()->toBase()
        ->where("$orders_table.id", "=", $order->id)
        ->join($purchases_table, "$orders_table.id", "=", "$purchases_table.order_id")
        ->join($items_table, "$items_table.name", "=", "$purchases_table.item_name")
        ->selectRaw(
            "$purchases_table.item_name,
            SUM($purchases_table.quantity) AS quantity,
            SUM($items_table.price) AS price,
            SUM($purchases_table.quantity * $items_table.price) AS total_price"
        )->groupBy("$purchases_table.item_name")
        ->get();

        return $purchases;
    }

}
