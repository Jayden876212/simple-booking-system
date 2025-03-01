@extends("layouts.default", ["page_title" => $page_title])

@section("scripts")
<script src="{{ asset("js/timeslots.js") }}" type="text/javascript"></script>
@stop

@section("content")

@section("title", $page_title)

@php
    enum TimeslotStatus {
        case IN_THE_PAST;
        case UNAVAILABLE;
        case AVAILABLE;
    }
@endphp

<h1 class="text-center mx-auto mb-5">{{ $page_title }}</h1>

<article class="container-fluid">
    <section class="row mb-5">
        <div class="col-md-5 mx-auto">
            <form class="card" action="" method="POST">
                @csrf

                <div class="card-header">
                    <h2>Book a Reservation</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bookingDate" class="form-label">Enter the date of your booking:</label>
                        <input class="form-control" value="{{ date("Y-m-d") }}" type="date" name="booking_date" id="bookingDate" required min="{{ date("Y-m-d") }}" onchange="
                            getUnavailableTimeslots('{{ route('get-unavailable-timeslots') }}', this.value, disableUnavailableTimeslots)
                        ">
                    </div>
                    <div class="mb-3">
                        <label for="timeslotStartTime" class="form-label">Choose a timeslot for your reservation:</label>
                        <select name="timeslot_start_time" id="timeslotStartTime" class="form-select" required>
                            @if (isset($timeslots) && isset($unavailable_timeslots))
                                <option id="timeslotPlaceholder" selected value="">Please choose a timeslot</option>
                                @foreach ($timeslots as $timeslot)
                                    @php
                                        $timeslot_start_time = (new DateTime(
                                            $timeslot["timeslot_start_time"])
                                        )->format("H:i");
                                    @endphp
                                    @if (isset($unavailable_timeslots))
                                        @php
                                            $timeslot_status = TimeslotStatus::AVAILABLE;
                                            if (strtotime(date("Y-m-d") . "T" . $timeslot_start_time . "Z") < time()) {
                                                $timeslot_status = TimeslotStatus::IN_THE_PAST;
                                            } else {
                                                foreach ($unavailable_timeslots as $unavailable_timeslot) {
                                                    if ($unavailable_timeslot["timeslot_start_time"] == $timeslot["timeslot_start_time"]) {
                                                        $timeslot_status = TimeslotStatus::UNAVAILABLE;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @switch ($timeslot_status)
                                            @case(TimeslotStatus::IN_THE_PAST)
                                                <option value="{{ $timeslot["timeslot_start_time"] }}" disabled>{{ $timeslot_start_time }}</option>
                                                @break
                                            @case(TimeslotStatus::UNAVAILABLE)
                                                <option value="{{ $timeslot["timeslot_start_time"] }}" disabled class="text-warning">{{ $timeslot_start_time }}</option>
                                                @break
                                            @case(TimeslotStatus::AVAILABLE)
                                                <option value="{{ $timeslot["timeslot_start_time"] }}">{{ $timeslot_start_time }}</option>
                                                @break
                                        @endswitch
                                    @else
                                        @php
                                            $timeslot_status = TimeslotStatus::AVAILABLE;
                                            if (strtotime(date("Y-m-d") . "T" . $timeslot_start_time . "Z") < time()) {
                                                $timeslot_status = TimeslotStatus::IN_THE_PAST;
                                            }
                                        @endphp
                                        @switch ($timeslot_status)
                                            @case(TimeslotStatus::IN_THE_PAST)
                                                <option value="{{ $timeslot["timeslot_start_time"] }}" disabled>{{ $timeslot_start_time }}</option>
                                                @break
                                            @case(TimeslotStatus::AVAILABLE)
                                                <option value="{{ $timeslot["timeslot_start_time"] }}">{{ $timeslot_start_time }}</option>
                                                @break
                                        @endswitch
                                    @endif
                                @endforeach
                            @else
                                <option id="timeslotPlaceholder" selected value="">No time slots available</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="create_booking" id="createBooking" value="true" class="btn btn-success float-end">Book</button>
                </div>
            </form>
        </div>
    </section>

    <section class="row mb-5">
        <div class="col-md-5 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h2>See Your Bookings</h2>
                </div>
                <ul class="list-group list-group-flush">
                    @if (isset($bookings))
                        @if (is_object($bookings))
                            @if (isset($bookings))
                                @foreach ($bookings as $booking)
                                    <li class="list-group-item d-flex flex-row justify-content-between">
                                        <p>
                                            Booking #{{ $booking["id"] }} - {{ $booking["timeslot_start_time"] }} {{ $booking["booking_date"] }}
                                        </p>
                                        <div class="d-flex flex-row gap-3">
                                            <a href="{{ route("bookings.cancel", ["booking_id" => $booking["id"]]) }}">
                                                <button class="btn btn-danger">
                                                    Cancel
                                                </button>
                                            <a href="{{ route("orders.show", ["booking_id" => $booking["id"]]) }}">
                                                <button class="btn btn-primary">
                                                    Order
                                                </button>
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <div class="card-body">
                                    You have not made any bookings yet.
                                </div>
                            @endif
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </section>
</article>

@stop