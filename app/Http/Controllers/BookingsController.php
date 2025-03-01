<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Booking;
use App\Models\Timeslot;

class BookingsController extends Controller
{

    public function showBookings()
    {
        if (! Auth::check()) {
            return redirect("account/login")->with("error", "User is logged out.");
        }

        $timeslots = Timeslot::getTimeslots();
        $bookings = Booking::getBookings(Auth::user()["username"]);
        $unavailable_timeslots = Booking::getUnavailableTimeslots(date("Y-m-d"));

        return view(
            "pages/bookings",
            [
                "timeslots" => $timeslots,
                "bookings" => $bookings,
                "unavailable_timeslots" => $unavailable_timeslots,
                "new_bookings" => User::find(Auth::id())->bookings(),
                "new_orders" => User::find(Auth::id())->orders()
            ]
        )->with("page_title", "Bookings");;
    }

    public function makeBooking(Request $request) {
        if (! Auth::check()) {
            return redirect("account/login")->with("error", "User is logged out.");
        }

        $request->validate([
            "booking_date" => ["required"],
            "timeslot_start_time" => ["required"]
        ]);

        $booking_date = $request->booking_date;
        $timeslot_start_time = $request->timeslot_start_time;
        Booking::createBooking($timeslot_start_time, $booking_date, Auth::user()["username"]);

        return redirect("/bookings")->with("success", "Booking successful!");
    }

    public function cancelBooking(Request $request) {
        $booking_to_be_cancelled = $request->booking_id ?? FALSE;

        if (!$booking_to_be_cancelled) {
            return redirect("bookings")->with("error", "You must provide the ID of the booking that you want to cancel.");
        }

        Booking::cancelBooking($booking_to_be_cancelled, Auth::user()["username"]);

        return redirect("bookings")->with("success", "Successfully cancelled booking!");
    }
}