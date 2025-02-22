<?php
    const PAGE_TITLE = "Orders";
    include_once "include/base.php";
?>

<h1><?=PAGE_TITLE?></h1>
<article>
    <form action="" method="POST">
        <h2>Choose your existing booking</h2>
        <section>
            <select id="booking" name="booking">
                <?php if (is_object($bookings)): ?>
                    <?php if (isset($bookings->result) && (! $bookings->error)): ?>
                        <option value="" selected id="bookingOptionPlaceholder">Please select a booking</option>
                        <?php foreach ($bookings->result as $booking): ?>
                            <option value="<?=$booking["booking_id"]?>">Booking #<?=$booking["booking_id"]?> (<?=$booking["booking_date"]?> <?=$booking["timeslot_start_time"]?>)</option>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endif ?>
            </select>
        </section>

        <h2>Add items to your order</h2>
        <section class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <h3>Name</h3>
                </div>
                <div class="col-md-3">
                    <h3>Price</h3>
                </div>
                <div class="col-md-3">
                    <h3>Quantity</h3>
                </div>
                <div class="col-md-3">
                    <h3>Total</h3>
                </div>
            </div>
            <?php if (is_object($items)): ?>
                <?php if (isset($items->result) && (! $items->error)): ?>
                    <?php foreach ($items->result as $item): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <p id="<?=$item["item_name"]?>"><?=$item["item_name"]?></p>
                            </div>
                            <div class="col-md-3">
                                <p id="price_of_<?=$item["item_name"]?>" name="price_of_<?=$item["item_name"]?>"><?=$item["price"]?></p>
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="quantity_of_<?=$item["item_name"]?>" name="quantity_of_<?=$item["item_name"]?>" value="0">
                            </div>
                            <div class="col-md-3">
                                <p>0</p>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
            <?php endif ?>
        </section>

        <h2>Finished ordering?</h2>
        <section>
            <button type="submit">Order Items</button>
        </section>
    </form>
</article>

<?php include_once "include/footer.php" ?>