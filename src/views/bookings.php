<?php
    const PAGE_TITLE = "Bookings";
    include_once "include/base.php";

    enum TimeslotStatus {
        case IN_THE_PAST;
        case UNAVAILABLE;
        case AVAILABLE;
    }
?>

<h1><?=PAGE_TITLE?></h1>

<form action="" method="POST">
    <input value="<?=date("Y-m-d")?>" type="date" name="booking_date" id="bookingDate" required min="<?=date("Y-m-d")?>" onchange="
        getUnavailableTimeslots('<?=HOST?><?=WORKING_DIRECTORY?>/bookings/get-unavailable-timeslots', this.value, disableUnavailableTimeslots)
    ">
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
                    <option value="<?=$timeslot["timeslot_start_time"]?>"><?=$timeslot_start_time?></option>
                <?php endif ?>
            <?php endforeach ?>
        <?php elseif (isset($timeslots->error) or isset($unavailable_timeslots->error)): ?>
            <option id="timeslotPlaceholder" selected value="">No time slots available <?=$timeslots->error?> <?=$unavailable_timeslots->error?></option>
        <?php else: ?>
            <option id="timeslotPlaceholder" selected value="">No time slots available</option>
        <?php endif ?>
    </select>
    <button type="submit" name="create_booking" id="createBooking" value="true">Book</button>
</form>

<?php include_once "include/footer.php" ?>