<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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

    public static function createBooking(string $timeslot_start_time, string $booking_date, User $user): Booking
    {
        $username = $user["username"];

        $booking = self::create([
            "timeslot_start_time" => $timeslot_start_time,
            "username" => $username,
            "booking_date" => $booking_date
        ]);
        
        return $booking;
    }

    public static function getUnavailableTimeslots(string $booking_date): Collection
    {
        $unavailable_timeslots = self::selectRaw(
            "COUNT(id) AS number_of_tables_booked, timeslot_start_time"
        )->where("booking_date", $booking_date)
        ->groupBy("timeslot_start_time")
        ->having("number_of_tables_booked", ">=", 10)
        ->get();

        return $unavailable_timeslots;
    }

    public static function getBookings(User $user): Collection
    {
        $bookings = $user->bookings()->where([
            ["booking_date", ">=", DB::raw("CURDATE()")]
        ])->get(
            ["id", "timeslot_start_time", "booking_date"]
        );

        return $bookings;
    }

    public static function getBooking(int $booking_id): Booking
    {
        $booking = self::get(
            ["id", "timeslot_start_time", "booking_date", "username"]
        )->where("id", $booking_id)->sole();

        return $booking;
    }
    
    public static function cancelBooking(int $booking_id): Booking
    {
        $booking_to_be_cancelled = self::where([
            ["id", $booking_id]
        ]);
        $booking_to_be_cancelled->delete();

        return $booking_to_be_cancelled;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
