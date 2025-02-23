<?php
    const PAGE_TITLE = "Orders";
    include_once "include/base.php";

    $booking_id = $_REQUEST["booking_id"] ?? FALSE;
?>

<h1 class="mx-auto text-center"><?=PAGE_TITLE?></h1>
<article class="container-fluid">
    <div class="row">
        <section class="col-md-5 mx-auto">
            <form class="card" action="" method="POST">
                <div class="card-header">
                    <h2>Order Items</h2>
                </div>
                <section class="list-group list-group-flush">
                    <div class="list-group-item">
                        <h3 class="card-title"><label class="form-label" for="booking">Choose your existing booking:</label></h3>
                        <section>
                            <select id="booking" name="booking" class="form-select">
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
                    </div>

                    <div class="list-group-item">
                        <h3 class="card-title">Add items to your order:</h3>
                    </div>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsList">
                                        <?php if (isset($items)): ?>
                                            <?php if (is_object($items)): ?>
                                                <?php if (isset($items->result) && (! $items->error)): ?>
                                                    <?php foreach ($items->result as $item): ?>
                                                        <tr id="<?=$item["item_name"]?>" class="item-product">
                                                            <th scope="row" class="col-md-3">
                                                                <p class="item-name" id="name_of_<?=$item["item_name"]?>"><?=$item["item_name"]?></p>
                                                            </th>
                                                            <td class="col-md-3">
                                                                <p>
                                                                    £<i
                                                                        class="item-price"
                                                                        id="price_of_<?=$item["item_name"]?>"
                                                                        title="Price of <?=$item["item_name"]?>"
                                                                    >
                                                                        <?=$item["price"]?>
                                                                    </i>
                                                                </p>
                                                            </td>
                                                            <td class="col-md-3">
                                                                <input
                                                                    class="item-quantity form-control"
                                                                    type="number"
                                                                    id="quantity_of_<?=$item["item_name"]?>"
                                                                    name="quantity_of_<?=$item["item_name"]?>"
                                                                    value="0"
                                                                    onchange="updateItems()"
                                                                    title="Select the amount of <?=$item["item_name"]?>(s) that you want"
                                                                >
                                                            </td>
                                                            <td class="col-md-3">
                                                                <p>
                                                                    £<i
                                                                        class="item-total"
                                                                        id="total_of_<?=$item["item_name"]?>"
                                                                        title="Total price of <?=$item["item_name"]?>"
                                                                    >
                                                                        0
                                                                    </i>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                <?php endif ?>
                                            <?php endif ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="card-footer">
                    <div class="row p-3">
                        <section class="alert alert-info">
                            <h4>Summary</h4>
                            <ul class="list-group bg-info bg-transparent">
                                <li class="list-group-item bg-transparent text-info-emphasis">
                                    <b>Overall Total: </b> £<i id="overallTotalPrice">0</i>
                                </li>
                                <li class="list-group-item bg-transparent text-info-emphasis">
                                    <b>Overall Quantity: </b> <i id="overallQuantity">0</i>
                                </li>
                            </ul>
                        </section>
                    </div>
                    <div class="row">
                        <section class="d-flex flex-row justify-content-between">
                            <h3 class="fs-5">
                                <label for="orderItems">Finished ordering?</label>
                            </h2>
                            <button id="orderItems" name="order_items" type="submit" class="btn btn-success" value="true">Order Items</button>
                        </section>
                    </div>
                </div>
            </form>
        </div>
    </div>
</article>

<?php include_once "include/footer.php" ?>