<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\User;
use App\Rules\BookingBelongsToUser;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    protected $auth;
    protected $user;
    protected $bookings;

    public function __construct(User $user, Guard $auth, Booking $bookings)
    {
        $this->auth = $auth;
        $this->user = $user->find($auth->id());

        $this->bookings = $bookings;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->auth->check()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "booking" => [
                "required",
                "exists:bookings,id",
                new BookingBelongsToUser($this->bookings, $this->user)
            ],
            "items" => [
                "required"
            ],
            "items.*" => [
                "required",
            ]
        ];
    }
}