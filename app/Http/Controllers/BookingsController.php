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

        $create_booking = $_POST["create_booking"] ?? FALSE;
        $timeslot_start_time = $_POST["timeslot_start_time"] ?? FALSE;
        $booking_date = $_POST["booking_date"] ?? FALSE;
        $form_submitted = $create_booking AND $booking_date AND $timeslot_start_time;
        if ($form_submitted) {
            $created_booking = Booking::createBooking($timeslot_start_time, $booking_date, Auth::user()["username"]);
        }

        return view(
            "pages/booking",
            [
                "timeslots" => $timeslots,
                "bookings" => $bookings,
                "unavailable_timeslots" => $unavailable_timeslots
            ]
        )->with("page_title", "Bookings");;
    }
}

class TimeslotController
{
    private $database;
    private $session;

    public function __construct(Database $database, Session $session) {
        $this->database = $database;
        $this->session = $session;
    }

    public function handleRequest() {
        $chosen_booking_date = $_REQUEST["booking_date"] ?? FALSE;
        if ($chosen_booking_date) {
            $booking = new Booking($this->database, $this->session);
            $unavailable_timeslots = $booking->getUnavailableTimeslots($chosen_booking_date);
            if (! isset($unavailable_timeslots->error) && isset($unavailable_timeslots->result)) {
                $unavailable_timeslots_processed = array_column($unavailable_timeslots->result, "number_of_tables_booked", "timeslot_start_time");
                $unavailable_timeslots_json =  json_encode($unavailable_timeslots_processed);
                echo $unavailable_timeslots_json;
            }
        }
        exit;
    }
}
