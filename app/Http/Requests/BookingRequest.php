<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    protected $auth;    

    public function __construct(Guard $auth) {
        $this->auth = $auth;
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
            "booking_date" => ["required"],
            "timeslot_start_time" => ["required"]
        ];
    }
}
