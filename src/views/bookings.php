<?php
    const PAGE_TITLE = "Bookings";
    include_once "include/base.php";
?>

<h1><?=PAGE_TITLE?></h1>

<form action="" method="POST">
    <input type="date" name="booking_date" id="bookingDate">
    <select name="timeslot_start_time" id="timeslotStartTime">
        <?php if (is_object($timeslots) && isset($timeslots->result) && (! isset($timeslots->error))): ?>
            <?php foreach ($timeslots->result as $timeslot): ?>
                <?php
                    $timeslot_start_time = (new DateTime(
                        $timeslot["timeslot_start_time"])
                    )->format("H:i");
                ?>
                <option value="<?=$timeslot["timeslot_start_time"]?>"><?=$timeslot_start_time?></option>
            <?php endforeach ?>
        <?php elseif (isset($timeslots->error)): ?>
            <option>No time slots available <?=$timeslots->error?></option>
        <?php else: ?>
            <option>No time slots available</option>
        <?php endif ?>
    </select>
    <button type="submit" name="create_booking" id="createBooking" value="true">Book</button>
</form>

<?php include_once "include/footer.php" ?>