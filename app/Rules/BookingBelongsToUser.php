<?php

namespace App\Rules;

use App\Models\Booking;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BookingBelongsToUser implements ValidationRule
{
    protected $bookings;
    protected $user;

    public function __construct(Booking $bookings, User $user) {
        $this->bookings = $bookings;
        $this->user = $user;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $booking = $this->bookings->getBooking($value);
        if ($booking->username != $this->user->username) {
            $fail("The booking belongs to another user.");
        }
    }
}
