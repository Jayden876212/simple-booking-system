<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = "name";
    protected $keyType = "string";
    public $incrementing = false;
    protected $table = "items";
    protected $fillable = [
        "name",
        "description",
    ];
    public $timestamps = false;

    public static function getItems() {
        $items = Item::get(["name", "price"]);

        return $items;
    }

    public static function getItem($item_name) {
        $item = Item::where("name", $item_name)->get(["name", "price"]);

        return $item;
    }
}