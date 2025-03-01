<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $primaryKey = "item_order_id";
    protected $table = "item_orders";
    protected $fillable = [
        "item_order_id",
        "item_name",
        "order_id",
        "quantity"
    ];

    public $timestamps = false;
}
