<?php
    const PAGE_TITLE = "Orders";
    include_once "include/base.php";

    $booking_id = $_REQUEST["booking_id"] ?? FALSE;
?>

<h1 class="mx-auto text-center"><?=PAGE_TITLE?></h1>
<article class="container-fluid">
    <div class="row">
        <div class="col-md-5 mx-auto">
            <form class="card" action="" method="POST">
                <div class="card-header">
                    <h2>Order Items</h2>
                </div>
                <section class="list-group list-group-flush">
                    <div class="list-group-item">
                        <h3 class="card-title">Choose your existing booking</h3>
                    </div>
                    <section class="list-group-item">
                        <select id="booking" name="booking">
                            <?php if (is_object($bookings)): ?>
                                <?php if (isset($bookings->result) && (! $bookings->error)): ?>
                                    <option
                                        value="" 
                                        id="bookingOptionPlaceholder"
                                        <?php if (! $booking_id): ?>
                                            selected
                                        <?php endif?>
                                    >
                                        Please select a booking
                                    </option>
                                    <?php foreach ($bookings->result as $booking): ?>
                                        <option
                                            value="<?=$booking["booking_id"]?>"
                                            <?php if ($booking["booking_id"] == $booking_id): ?>
                                                selected
                                            <?php endif ?>
                                        >
                                            Booking #<?=$booking["booking_id"]?> (<?=$booking["booking_date"]?> <?=$booking["timeslot_start_time"]?>)
                                        </option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            <?php endif ?>
                        </select>
                    </section>

                    <div class="list-group-item">
                        <h3 class="card-title">Add items to your order</h3>
                    </div>
                    <section class="container-fluid list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <h4>Name</h4>
                            </div>
                            <div class="col-md-3">
                                <h4>Price</h4>
                            </div>
                            <div class="col-md-3">
                                <h4>Quantity</h4>
                            </div>
                            <div class="col-md-3">
                                <h4>Total</h4>
                            </div>
                        </div>
                        <section id="itemsList">
                            <?php if (isset($items)): ?>
                                <?php if (is_object($items)): ?>
                                    <?php if (isset($items->result) && (! $items->error)): ?>
                                        <?php foreach ($items->result as $item): ?>
                                            <div id="<?=$item["item_name"]?>" class="row item-product">
                                                <div class="col-md-3">
                                                    <p class="item-name" id="name_of_<?=$item["item_name"]?>"><?=$item["item_name"]?></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p>
                                                        £<i class="item-price" id="price_of_<?=$item["item_name"]?>"><?=$item["price"]?></i>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <input class="item-quantity" type="number" id="quantity_of_<?=$item["item_name"]?>" name="quantity_of_<?=$item["item_name"]?>" value="0" onchange="
                                                        updateItems()
                                                    ">
                                                </div>
                                                <div class="col-md-3">
                                                    <p>£<i class="item-total" id="total_of_<?=$item["item_name"]?>">0</i></p>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                <?php endif ?>
                            <?php endif ?>
                        </section>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Summary</h4>
                                <p>
                                    <b>Overall Total: </b> £<i id="overallTotalPrice">0</i>
                                    <br>
                                    <b>Overall Quantity: </b> <i id="overallQuantity">0</i>
                                </p>
                            </div>
                        </div>
                    </section>
                </section>

                <div class="card-footer">
                    <section class="d-flex flex-row justify-content-between">
                        <h3 class="fs-5">
                            <label for="orderItems">Finished ordering?</label>
                        </h2>
                        <button id="orderItems" name="order_items" type="submit" class="btn btn-success">Order Items</button>
                    </section>
                </div>
            </form>
        </div>
    </div>
</article>

<?php include_once "include/footer.php" ?>