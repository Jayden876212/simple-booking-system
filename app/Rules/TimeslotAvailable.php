<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeslotAvailable implements ValidationRule
{
    protected $booking;
    protected $booking_date;

    public function __construct(Booking $booking, string $booking_date)
    {
        $this->booking = $booking;
        $this->booking_date = $booking_date;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $timeslot_start_time = $value;
        $booking_date = $this->booking_date;

        $unavailable_timeslots = $this->booking->getUnavailableTimeslots($booking_date);
        foreach ($unavailable_timeslots as $unavailable_timeslot) {
            if ($unavailable_timeslot["timeslot_start_time"] == $timeslot_start_time) {
                $fail("Timeslot at $value is unavailable (there cannot be more than 10 tables booked in a given timeslot)");
            }
        }
    }
}
