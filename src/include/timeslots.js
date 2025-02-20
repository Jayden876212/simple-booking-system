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
    })
}

function disableUnavailableTimeslots(data) {
    let timeslotStartTimeInput = $("#timeslotStartTime");
    options = timeslotStartTimeInput.children();
    options.each(function() {
        if (data.hasOwnProperty(this.value)) {
            number_of_tables_booked = data[this.value];
            console.log(number_of_tables_booked + this);
            this.setAttribute("disabled", "true")
            this.classList.add("text-warning");
        } else {
            console.log(this);
            this.removeAttribute("disabled")
            this.classList.remove("text-warning");
        }
    });
}

let url = `${HOST}${WORKING_DIRECTORY}/bookings/get-unavailable-timeslots`;
let booking_date = "2025-04-03"
getUnavailableTimeslots(url, booking_date, disableUnavailableTimeslots);