<?php
    const PAGE_TITLE = "Orders";
    include_once "include/base.php";
?>

<h1><?=PAGE_TITLE?></h1>
<form action="" method="POST">
    <section>
        <select id="booking" name="booking">
            <?php if (is_object($bookings)): ?>
                <?php if (isset($bookings->result) && (!$bookings->error)): ?>
                    <option value="" selected id="bookingOptionPlaceholder">Please select a booking</option>
                    <?php foreach ($bookings->result as $booking): ?>
                        <!-- booking_id, timeslot_start_time, booking_date -->
                        <option value="<?=$booking["booking_id"]?>">Booking #<?=$booking["booking_id"]?> (<?=$booking["booking_date"]?> <?=$booking["timeslot_start_time"]?>)</option>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endif ?>
        </select>
    </section>
</form>

<?php include_once "include/footer.php" ?>