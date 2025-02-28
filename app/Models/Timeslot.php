<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $primaryKey = "timeslot_start_time";
    protected $keyType = "string";
    public $incrementing = false;

    protected $table = "timeslots";
    public $timestamps = false;

    public static function getTimeslots() {
        $timeslots = self::get("timeslot_start_time");
        return $timeslots;
    }

    public static function getTimeslot($timeslot_start_time) {
        $timeslot = self::where("timeslot_start_time", $timeslot_start_time)->get("timeslot_start_time");
        return $timeslot;
    }

    public static function getOrderedTimeslots() {
        $ordered_timeslots = self::orderBy("timeslot_start_time", "asc")->get("timeslot_start_time");
        return $ordered_timeslots;
    }
}