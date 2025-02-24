<?php
    const PAGE_TITLE = "Orders";
    include_once "include/base.php";

    $booking_id = $_REQUEST["booking_id"] ?? FALSE;
?>

<h1 class="mx-auto text-center"><?=PAGE_TITLE?></h1>
<article class="container-fluid">
    <div class="row">
        <section class="col-md-5 mx-auto mb-4">
            <form class="card" action="" method="POST">
                <div class="card-header d-flex flex-row justify-content-between">
                    <h2>Order Items</h2>
                    <div class="p-1">
                        <button type="button" class="btn btn-warning position-relative">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                <i id="overallQuantityCart">0</i>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </button>
                    </div>
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
                            <h4>
                                Summary
                            </h4>
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
        </section>
    </div>
    <div class="row">
        <section class="col-md-5 mx-auto mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>View Your Orders</h2>
                </div>
                <div class="card-body">
                    <?php if (is_object($orders)): ?>
                        <?php if (isset($orders->result)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <th scope="col">ID</th>
                                        <th scope="col">Date & Time Ordered</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Details</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders->result as $order): ?>
                                            <tr>
                                                <th scope="row">
                                                    #<?=$order["order_id"]?>
                                                </th>
                                                <td>
                                                    <?=$order["datetime_ordered"]?>
                                                </td>
                                                <td>
                                                    £<?=$order["total_price"]?>
                                                </td>
                                                <td>
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderDetails_<?=$order["order_id"]?>">
                                                        View
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="orderDetails_<?=$order["order_id"]?>" tabindex="-1" aria-labelledby="orderDetailsLabel_<?=$order["order_id"]?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h3 class="modal-title fs-5" id="orderDetailsLabel_<?=$order["order_id"]?>">View Order #<?=$order["order_id"]?></h3>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?php
                                                                        $order_summary = $orders_and_items[$order["order_id"]]->result;
                                                                    ?>
                                                                    <?php foreach ($order_summary as $item): ?>
                                                                        <ul class="list-group m-3">
                                                                            <li class="list-group-item d-flex flex-row justify-content-between list-group-item-info">
                                                                                <p>
                                                                                    Item Name:
                                                                                </p>
                                                                                <b>
                                                                                    <?=$item["item_name"]?>
                                                                                </b>
                                                                            </li>
                                                                            <li class="list-group-item d-flex flex-row justify-content-between">
                                                                                <p>
                                                                                    Price:
                                                                                </p>
                                                                                <i>
                                                                                    <?=$item["price"]?>
                                                                                </i>
                                                                            </li>
                                                                            <li class="list-group-item d-flex flex-row justify-content-between">
                                                                                <p>
                                                                                    Quantity:
                                                                                </p>
                                                                                <i>
                                                                                    <?=$item["quantity"]?>
                                                                                </i>
                                                                            </li>
                                                                            <li class="list-group-item d-flex flex-row justify-content-between">
                                                                                <p>
                                                                                    Total Price:
                                                                                </p>
                                                                                <i>
                                                                                    <?=$item["total_price"]?>
                                                                                </i>
                                                                            </li>
                                                                        </ul>
                                                                    <?php endforeach ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="card-text">No orders were found.</p>
                        <?php endif ?>
                    <?php else: ?>
                        <p class="card-text">No orders were found.</p>
                    <?php endif ?>
                </div>
            </div>
        </section>
    </div>
</article>

<?php include_once "include/footer.php" ?>