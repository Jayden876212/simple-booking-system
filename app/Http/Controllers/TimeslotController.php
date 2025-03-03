<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Booking;

class TimeslotController extends Controller
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handleRequest(Request $request): JsonResponse {
        $chosen_booking_date = $request->booking_date ?? FALSE;
        if ($chosen_booking_date) {
            $unavailable_timeslots = $this->booking->getUnavailableTimeslots($chosen_booking_date);
            if (isset($unavailable_timeslots)) {
                $unavailable_timeslots_processed = array_column($unavailable_timeslots->toArray(), "number_of_tables_booked", "timeslot_start_time");
                return response()->json($unavailable_timeslots_processed);
            }
        }
        return response()->json([]);
    }
}