<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $table = "item_orders";
    protected $fillable = [
        "id",
        "item_name",
        "order_id",
        "quantity"
    ];

    public $timestamps = false;
}
