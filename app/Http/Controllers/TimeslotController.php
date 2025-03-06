<?php

namespace App\Http\Controllers;

use App\Models\Timeslot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Booking;

class TimeslotController extends Controller
{
    protected $bookings;
    protected $timeslots;

    public function __construct(Booking $bookings, Timeslot $timeslots)
    {
        $this->bookings = $bookings;
        $this->timeslots = $timeslots;
    }

    public function handleRequest(Request $request): JsonResponse {
        $chosen_booking_date = $request->booking_date ?? FALSE;
        if ($chosen_booking_date) {
            $unavailable_timeslots = $this->timeslots->getUnavailableTimeslots($chosen_booking_date, $this->bookings);
            if (isset($unavailable_timeslots)) {
                $unavailable_timeslots_processed = array_column($unavailable_timeslots->toArray(), "number_of_tables_booked", "timeslot_start_time");
                return response()->json($unavailable_timeslots_processed);
            }
        }
        return response()->json([]);
    }
}