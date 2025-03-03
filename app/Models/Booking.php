<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use App\Models\Timeslot;
use DateTime;
use Exception;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $table = "bookings";
    protected $fillable = [
        "timeslot_start_time",
        "username",
        "booking_date"
    ];
    public $timestamps = false;

    public static function createBooking($timeslot_start_time, $booking_date, $username) {
        $booking = Booking::create([
            "timeslot_start_time" => $timeslot_start_time,
            "username" => $username,
            "booking_date" => $booking_date
        ]);
        
        return $booking;
    }

    public static function getUnavailableTimeslots($booking_date) {
        $unavailable_timeslots = Booking::selectRaw(
            "COUNT(id) AS number_of_tables_booked, timeslot_start_time"
        )->where("booking_date", $booking_date)
        ->groupBy("timeslot_start_time")
        ->having("number_of_tables_booked", ">=", 10)
        ->get();

        return $unavailable_timeslots;
    }

    public static function getBookings($username) {
        $bookings = Booking::select(
            "id", "timeslot_start_time", "booking_date"
        )->where([
            ["booking_date", ">=", DB::raw("CURDATE()")],
            ["username", $username]
        ])->get();

        return $bookings;
    }

    public static function getBooking($booking_id) {
        $booking = Booking::get(
            ["id", "timeslot_start_time", "booking_date", "username"]
        )->where("id", $booking_id)->sole();

        return $booking;
    }
    
    public static function cancelBooking($booking_id, $username) {
        $booking_to_be_cancelled = Booking::where([
            ["id", $booking_id],
            ["username", $username]
        ]);
        $booking_to_be_cancelled->delete();

        return $booking_to_be_cancelled;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
