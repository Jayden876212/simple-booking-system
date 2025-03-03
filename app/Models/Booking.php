<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use App\Models\Timeslot;
use DateTime;
use Exception;
use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

enum BookingError: string {
    case BOOKING_DATE_EMPTY = "Booking date is empty."; // Presence check
    case TIMESLOT_START_TIME_EMPTY = "Timeslot is empty."; // Presence check
    case BOOKING_DATE_INCORRECT_FORMAT = "Booking date is in the incorrect format (must be YYYY-MM-DD)"; // Format check
    case TIMESLOT_INCORRECT_FORMAT = "Timeslot is in the incorrect format (must be HH:mm:ss)"; // Format check
    case BOOKING_DATE_IN_PAST = "Booking date is in the past"; // Consistency check
    case TIMESLOT_IN_PAST = "Booking date is today but timeslot is in the past"; // Consistency check
    case TIMESLOT_NOT_EXIST = "The timeslot is not available from the selection menu"; // Look up check
    case UNAVAILABLE_TIMESLOT = "Timeslot is unavailable (there cannot be more than 10 tables booked in a given timeslot)"; // Look up check
}

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
        $unavailable_timeslots = self::getUnavailableTimeslots($booking_date);
        $specific_timeslot_unavailable = FALSE;
        foreach ($unavailable_timeslots as $unavailable_timeslot) {
            if ($unavailable_timeslot["timeslot_start_time"] == $timeslot_start_time) {
                $specific_timeslot_unavailable = TRUE;
                break;
            }
        }

        if ($specific_timeslot_unavailable) {
            throw new Exception(BookingError::UNAVAILABLE_TIMESLOT->value, 1);
        }

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
