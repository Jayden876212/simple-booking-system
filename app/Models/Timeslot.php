<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Timeslot extends Model
{
    protected $primaryKey = "timeslot_start_time";
    protected $keyType = "string";
    public $incrementing = false;

    protected $table = "timeslots";
    public $timestamps = false;

    public static function getTimeslots(): Collection {
        $timeslots = self::get("timeslot_start_time");
        return $timeslots;
    }

    public static function getTimeslot($timeslot_start_time): Timeslot {
        $timeslot = self::where("timeslot_start_time", $timeslot_start_time)->get("timeslot_start_time")->sole();
        return $timeslot;
    }

    public static function getOrderedTimeslots(): Collection {
        $ordered_timeslots = self::orderBy("timeslot_start_time", "asc")->get("timeslot_start_time");
        return $ordered_timeslots;
    }
}