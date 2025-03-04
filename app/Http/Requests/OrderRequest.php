<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Item;
use App\Models\Timeslot;
use App\Models\User;
use App\Rules\BookingBelongsToUser;
use App\Rules\OrderAtLeastOneItem;
use App\Rules\OrderInTimeslot;
use App\Rules\OrderItemsExist;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    protected $auth;
    protected $user;

    protected $bookings;
    protected $items;
    protected $timeslots;

    public function __construct(User $user, Guard $auth, Booking $bookings, Item $items, Timeslot $timeslots)
    {
        $this->auth = $auth;
        $this->user = $user->find($auth->id());

        $this->bookings = $bookings;
        $this->items = $items;
        $this->timeslots = $timeslots;
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
                new BookingBelongsToUser($this->bookings, $this->user),
                new OrderInTimeslot($this->bookings, $this->timeslots)
            ],
            "items" => [
                "required",
                new OrderAtLeastOneItem(),
                new OrderItemsExist($this->items)
            ],
            "items.*" => [
                "required",
                "gte:0"
            ]
        ];
    }
}