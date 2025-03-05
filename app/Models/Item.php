<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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

    public static function getItems(): Collection {
        $items = Item::get(["name", "price"]);

        return $items;
    }

    public static function getItem($item_name): Item {
        $item = Item::where("name", $item_name)->get(["name", "price"])->sole();

        return $item;
    }
}