<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = "item_name";
    protected $keyType = "string";
    public $incrementing = false;
    protected $table = "items";
    protected $fillable = [
        "item_name",
        "description",
    ];
    public $timestamps = false;

    public static function getItems() {
        $items = Item::get(["item_name", "price"]);

        return $items;
    }

    public static function getItem($item_name) {
        $item = Item::where("item_name", $item_name)->get(["item_name", "price"]);

        return $item;
    }
}