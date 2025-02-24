function getTotal(price, quantity) {
    let total = price * quantity;
    return total;
}

function updateItems() {
    let items = $("#itemsList").children();

    updateTotalPrices(items);
    updateOverallTotalPricesAndQuantities(items);
}

function updateTotalPrices(items) {
    items.each(function(index) {
        let itemPrice = $(this).find(".item-price");
        let itemQuantity = $(this).find(".item-quantity");
        let itemTotal = $(this).find(".item-total");
        let price = itemPrice.text();
        let quantity = itemQuantity.val();

        itemTotal.text(getTotal(price, quantity));
    });
}

function updateOverallTotalPricesAndQuantities(items) {
    let overallTotalPrice = 0;
    let overallQuantity = 0;
    items.each(function(index) {
        let itemQuantity = $(this).find(".item-quantity");
        let itemTotal = $(this).find(".item-total");
        let quantity = itemQuantity.val();
        let total = itemTotal.text();

        overallTotalPrice += Number(total);
        overallQuantity += Number(quantity);
    });

    $("#overallTotalPrice").text(overallTotalPrice);
    $("#overallQuantity").text(overallQuantity);
    $("#overallQuantityCart").text(overallQuantity);
}