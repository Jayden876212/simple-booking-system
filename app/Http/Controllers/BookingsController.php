<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Timeslot;

class BookingsController extends Controller
{
    protected $auth;
    protected $user;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->user = $auth->user();
    }

    public function showBookings(): RedirectResponse|View
    {
        if (! Auth::check()) {
            return redirect()->route("login.show")->with("error", "User is logged out.");
        }

        $timeslots = Timeslot::getTimeslots();
        $bookings = Booking::getBookings($this->user);
        $unavailable_timeslots = Booking::getUnavailableTimeslots(date("Y-m-d"));
        $orders = Order::getOrders(Auth::id());


        return view(
            "pages.bookings",
            [
                "timeslots" => $timeslots,
                "bookings" => $bookings,
                "unavailable_timeslots" => $unavailable_timeslots,
                "orders" => $orders->toArray()
            ]
        )->with("page_title", "Bookings");
    }

    public function makeBooking(BookingRequest $request): RedirectResponse {
        if (! Auth::check()) {
            return redirect()->route("login.show")->with("error", "User is logged out.");
        }

        $booking_date = $request->booking_date;
        $timeslot_start_time = $request->timeslot_start_time;
        Booking::createBooking($timeslot_start_time, $booking_date, $this->user);

        return redirect()->route("bookings.show")->with("success", "Booking successful!");
    }

    public function cancelBooking(Request $request): RedirectResponse {
        $booking_to_be_cancelled = $request->booking_id ?? FALSE;

        if (!$booking_to_be_cancelled) {
            return redirect()->route("bookings.show")->with("error", "You must provide the ID of the booking that you want to cancel.");
        }

        Booking::cancelBooking($booking_to_be_cancelled);

        return redirect()->route("bookings.show")->with("success", "Successfully cancelled booking!");
    }
}