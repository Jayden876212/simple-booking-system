<?php
    const PAGE_TITLE = "Bookings";
    include_once "include/base.php";
?>

<h1><?=PAGE_TITLE?></h1>

<form action="" method="POST">
    <input type="date" name="booking_date" id="bookingDate" onchange="
        getUnavailableTimeslots('<?=HOST?><?=WORKING_DIRECTORY?>/bookings/get-unavailable-timeslots', this.value, disableUnavailableTimeslots)
    " required>
    <select name="timeslot_start_time" id="timeslotStartTime" class="form-select" required>
        <option selected value="">Please choose a timeslot</option>
        <?php if (
            is_object($timeslots) && isset($timeslots->result) && (! isset($timeslots->error))
            && is_object($unavailable_timeslots) && isset($unavailable_timeslots->result) && (! isset($unavailable_timeslots->error))
        ): ?>
            <?php foreach ($timeslots->result as $timeslot): ?>
                <?php
                    $timeslot_start_time = (new DateTime(
                        $timeslot["timeslot_start_time"])
                    )->format("H:i");
                ?>
                <?php foreach ($unavailable_timeslots->result as $unavailable_timeslot): ?>
                    <?php if ($unavailable_timeslot["timeslot_start_time"] != $timeslot["timeslot_start_time"]): ?>
                        <option value="<?=$timeslot["timeslot_start_time"]?>"><?=$timeslot_start_time?></option>
                    <?php else: ?>
                        <option value="<?=$timeslot["timeslot_start_time"]?>" disabled class="text-warning"><?=$timeslot_start_time?></option>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endforeach ?>
        <?php elseif (isset($timeslots->error) or isset($unavailable_timeslots->error)): ?>
            <option>No time slots available <?=$timeslots->error?></option>
        <?php else: ?>
            <option>No time slots available</option>
        <?php endif ?>
    </select>
    <button type="submit" name="create_booking" id="createBooking" value="true">Book</button>
</form>

<?php include_once "include/footer.php" ?>