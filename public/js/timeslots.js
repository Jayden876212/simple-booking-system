function getUnavailableTimeslots(url, booking_date, callback) {
    var scriptReturnedData;
    $.ajax({
        url: url,
        type: "GET",
        data: {
            booking_date: booking_date
        },
        dataType: "json",
        success: function handleResponse(response) {
            callback(response);
            scriptReturnedData = true;
        }
    }).always(function() {
        if (!scriptReturnedData) {
            callback({});
        }
    });
}

function disableUnavailableTimeslots(data) {
    let timeslotStartTimeInput = $("#timeslotStartTime");
    let timeslotPlaceholder = $("#timeslotPlaceholder");
    let bookingDateInput = $("#bookingDate");


    timeslotStartTimeInput.val(timeslotPlaceholder.val());
    options = timeslotStartTimeInput.children();
    options.each(function() {
        // Get the unix time of both the booking date and timeslot put together
        let bookingAndTimeslotDateTime = Date.parse(`${bookingDateInput.val()}T${this.value}Z`);
        
        if (data.hasOwnProperty(this.value)) {
            number_of_tables_booked = data[this.value];
            this.setAttribute("disabled", "");
            this.classList.add("text-warning");
        } else if (bookingAndTimeslotDateTime < Date.now()) {
            // Check if the (booking and timeslot) datetime combined result is in the past so we can disable the options
            this.setAttribute("disabled", "");
            this.classList.remove("text-warning");
        } else {
            this.removeAttribute("disabled")
            this.classList.remove("text-warning");
        }
    });
}

let url = `get-unavailable-timeslots`;