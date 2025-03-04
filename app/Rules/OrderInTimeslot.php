<?php

namespace App\Rules;

use App\Models\Booking;
use App\Models\Timeslot;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderInTimeslot implements ValidationRule
{
    protected $bookings;
    protected $timeslots;

    private const MINUTE = 60;
    private const HOUR = 60 * self::MINUTE;

    public function __construct(Booking $bookings, Timeslot $timeslots)
    {
        $this->bookings = $bookings;
        $this->timeslots = $timeslots;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $booking_id = $value;
        $booking = $this->bookings->getBooking($booking_id);

        $ordered_timeslots = $this->timeslots->getOrderedTimeslots()->toArray();
        $timeslot_start_times = [];
        foreach ($ordered_timeslots as $timeslot) {
            $timeslot_start_times[] = $timeslot["timeslot_start_time"];
        }

        $start_time_key = array_search($booking["timeslot_start_time"], $timeslot_start_times);
       
        $valid_start_time = $ordered_timeslots[$start_time_key]["timeslot_start_time"];
        $valid_start_datetime = $booking["booking_date"] . " " . $valid_start_time;
        $valid_start_datetime_unix = strtotime($valid_start_datetime);

        try {
            $valid_end_time = $ordered_timeslots[$start_time_key + 1]["timeslot_start_time"];
            $valid_end_datetime_unix = strtotime($booking["booking_date"] . " " . $valid_end_time);
        } catch (Exception $e) {
            $valid_end_datetime_unix = strtotime($valid_start_datetime) + self::HOUR;
            $valid_end_time = date("H:i:s", $valid_end_datetime_unix);
        }

        $order_not_in_timeslot = (time() < $valid_start_datetime_unix) OR (time() >= $valid_end_datetime_unix);
        if ($order_not_in_timeslot) {
            $fail("Your order must be done at the time between your timeslot's start time ($valid_start_time) and the next timeslot's start time ($valid_end_time).");
        }
    }
}
