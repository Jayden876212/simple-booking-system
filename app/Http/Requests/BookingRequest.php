<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Rules\TimeslotAvailable;
use App\Rules\TimeslotInFuture;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    protected $auth; 
    protected $booking;

    public function __construct(Guard $auth, Booking $booking)
    {
        $this->auth = $auth;
        $this->booking = $booking;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user_logged_in = $this->auth->check();

        return $user_logged_in;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "booking_date" => [
                "required", // Presence check
                "date_format:Y-m-d", // Format check
                Rule::date()->todayOrAfter() // Consistency check
            ],
            "timeslot_start_time" => [
                "required", // Presence check
                "date_format:H:i:s", // Format check
                "exists:timeslots,timeslot_start_time", // Look up check
                new TimeslotInFuture($this->booking_date),
                new TimeslotAvailable($this->booking, $this->booking_date)
            ]
        ];
    }
}
