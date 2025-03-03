# Data Validation & Business Rules for Ordering

## Presence Checks

- User must enter a booking ID
- User must enter at least 1 item

## Look-up Checks

- Booking ID selected must exist
- Item names ordered must exist
- Booking username must be the same as the one in the session

## Range Checks

- Quantity cannot be less than 1 for every item selected

## Consistency Checks

- Order must be done at the time between the timeslot's timeslot_start_time and the next chronological timeslot's timeslot_start_time
