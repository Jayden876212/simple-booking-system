<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeslotInFuture implements ValidationRule
{
    protected $booking_date;

    public function __construct(string $booking_date)
    {
        $this->booking_date = $booking_date;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->booking_date == date("Y-m-d")) {
            $timeslot = strtotime($value);
            $timeslot_in_the_past = $timeslot < time();
            if ($timeslot_in_the_past) { // in the past
                $fail("The :attribute must be in the future if the booking date is today.");
            }
        }
    }
}
