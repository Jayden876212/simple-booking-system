<?php
    const PAGE_TITLE = "Bookings";
    include_once "include/base.php";

    enum TimeslotStatus {
        case IN_THE_PAST;
        case UNAVAILABLE;
        case AVAILABLE;
    }
?>

<h1 class="text-center mx-auto mb-5"><?=PAGE_TITLE?></h1>

<article class="container-fluid">
    <section class="row mb-5">
        <div class="col-md-5 mx-auto">
            <form class="card" action="" method="POST">
                <div class="card-header">
                    <h2>Book a Reservation</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bookingDate" class="form-label">Enter the date of your booking:</label>
                        <input class="form-control" value="<?=date("Y-m-d")?>" type="date" name="booking_date" id="bookingDate" required min="<?=date("Y-m-d")?>" onchange="
                            getUnavailableTimeslots('<?=HOST?><?=WORKING_DIRECTORY?>/bookings/get-unavailable-timeslots', this.value, disableUnavailableTimeslots)
                        ">
                    </div>
                    <div class="mb-3">
                        <label for="timeslotStartTime" class="form-label">Choose a timeslot for your reservation:</label>
                        <select name="timeslot_start_time" id="timeslotStartTime" class="form-select" required>
                            <?php if (
                                is_object($timeslots) && isset($timeslots->result) && (! isset($timeslots->error))
                                && is_object($unavailable_timeslots) && (! isset($unavailable_timeslots->error))
                            ): ?>
                                <option id="timeslotPlaceholder" selected value="">Please choose a timeslot</option>
                                <?php foreach ($timeslots->result as $timeslot): ?>
                                    <?php
                                        $timeslot_start_time = (new DateTime(
                                            $timeslot["timeslot_start_time"])
                                        )->format("H:i");
                                    ?>
                                    <?php if (isset($unavailable_timeslots->result)): ?>
                                        <?php
                                            $timeslot_status = TimeslotStatus::AVAILABLE;
                                            if (strtotime(date("Y-m-d") . "T" . $timeslot_start_time . "Z") < time()) {
                                                $timeslot_status = TimeslotStatus::IN_THE_PAST;
                                            } else {
                                                foreach ($unavailable_timeslots->result as $unavailable_timeslot) {
                                                    if ($unavailable_timeslot["timeslot_start_time"] == $timeslot["timeslot_start_time"]) {
                                                        $timeslot_status = TimeslotStatus::UNAVAILABLE;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php switch ($timeslot_status):
                                            case TimeslotStatus::IN_THE_PAST: ?>
                                                <option value="<?=$timeslot["timeslot_start_time"]?>" disabled><?=$timeslot_start_time?></option>
                                                <?php break;
                                            case TimeslotStatus::UNAVAILABLE: ?>
                                                <option value="<?=$timeslot["timeslot_start_time"]?>" disabled class="text-warning"><?=$timeslot_start_time?></option>
                                                <?php break;
                                            case TimeslotStatus::AVAILABLE: ?>
                                                <option value="<?=$timeslot["timeslot_start_time"]?>"><?=$timeslot_start_time?></option>
                                                <?php break;
                                        endswitch ?>
                                    <?php else: ?>
                                        <?php
                                            $timeslot_status = TimeslotStatus::AVAILABLE;
                                            if (strtotime(date("Y-m-d") . "T" . $timeslot_start_time . "Z") < time()) {
                                                $timeslot_status = TimeslotStatus::IN_THE_PAST;
                                            }
                                        ?>
                                        <?php switch ($timeslot_status):
                                            case TimeslotStatus::IN_THE_PAST: ?>
                                                <option value="<?=$timeslot["timeslot_start_time"]?>" disabled><?=$timeslot_start_time?></option>
                                                <?php break;
                                            case TimeslotStatus::AVAILABLE: ?>
                                                <option value="<?=$timeslot["timeslot_start_time"]?>"><?=$timeslot_start_time?></option>
                                                <?php break;
                                        endswitch ?>
                                    <?php endif ?>
                                <?php endforeach ?>
                            <?php elseif (isset($timeslots->error) or isset($unavailable_timeslots->error)): ?>
                                <option id="timeslotPlaceholder" selected value="">No time slots available <?=$timeslots->error?> <?=$unavailable_timeslots->error?></option>
                            <?php else: ?>
                                <option id="timeslotPlaceholder" selected value="">No time slots available</option>
                            <?php endif ?>
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
                    <?php if (isset($bookings)): ?>
                        <?php if (is_object($bookings)): ?>
                            <?php if (isset($bookings->result)): ?>
                                <?php foreach ($bookings->result as $booking): ?>
                                    <li class="list-group-item d-flex flex-row justify-content-between">
                                        <p>
                                            Booking #<?=$booking["booking_id"]?> - <?=$booking["timeslot_start_time"]?> <?=$booking["booking_date"]?>
                                        </p>
                                        <div class="d-flex flex-row gap-3">
                                            <a href="<?=HOST?><?=WORKING_DIRECTORY?>/bookings/cancel?booking_id=<?=$booking["booking_id"]?>">
                                                <button class="btn btn-danger">
                                                    Cancel
                                                </button>
                                            <a href="<?=HOST?><?=WORKING_DIRECTORY?>/bookings/orders?booking_id=<?=$booking["booking_id"]?>">
                                                <button class="btn btn-primary">
                                                    Order
                                                </button>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach ?>
                            <?php else: ?>
                                <div class="card-body">
                                    You have not made any bookings yet.
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </section>
</article>

<?php include_once "include/footer.php" ?>