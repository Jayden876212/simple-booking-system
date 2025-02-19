function getUnavailableTimeslots(url, booking_date) {
    let timeslotStartTime = $("timeslotStartTime")
    $.ajax({
        dataType: "json",
        url: `${url}?booking_date=${booking_date}`,
        data: data,
        success: console.log("Success")
    });
}

let url = `${HOST}${WORKING_DIRECTORY}/bookings/get-unavailable-timeslots`;
getUnavailableTimeslots(url);