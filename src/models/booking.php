<?php

enum BookingError: string {
    case BOOKING_DATE_EMPTY = "Booking date is empty."; // Presence check
    case TIMESLOT_START_TIME_EMPTY = "Timeslot is empty."; // Presence check
    case BOOKING_DATE_INCORRECT_FORMAT = "Booking date is in the incorrect format (must be YYYY-MM-DD)"; // Format check
    case TIMESLOT_INCORRECT_FORMAT = "Timeslot is in the incorrect format (must be HH:mm:ss)"; // Format check
    case BOOKING_DATE_IN_PAST = "Booking date is in the past"; // Consistency check
    case TIMESLOT_IN_PAST = "Booking date is today but timeslot is in the past"; // Consistency check
    case TIMESLOT_NOT_EXIST = "The timeslot is not available from the selection menu"; // Look up check
    case UNAVAILABLE_TIMESLOT = "Timeslot is unavailable (there cannot be more than 10 tables booked in a given timeslot)"; // Look up check
}

class Timeslot
{
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function getTimeslots() {
        $operation = new CrudOperation();

        try {
            $get_timeslots = $this->database->database_handle->prepare(
                "SELECT timeslot_start_time FROM timeslots"
            );
            $gotten_timeslots = $get_timeslots->execute();
            if ($gotten_timeslots) {
                $timeslots = $get_timeslots->fetchAll();
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslots.", CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslots.", CrudOperation::DATABASE_ERROR);
        }

        return $operation->createMessage("Fetched timeslots successfully.", CrudOperation::NO_ERRORS, $timeslots);
    }

    public function getTimeslot($timeslot_start_time) {
        $operation = new CrudOperation();

        try {
            $get_timeslot = $this->database->database_handle->prepare(
                "SELECT timeslot_start_time FROM timeslots
                WHERE timeslot_start_time = :timeslot_start_time"
            );
            $gotten_timeslot = $get_timeslot->execute([
                "timeslot_start_time" => $timeslot_start_time
            ]);
            if ($gotten_timeslot) {
                $timeslot = $get_timeslot->fetchAll();
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslot.", CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslot.", CrudOperation::DATABASE_ERROR);
        }

        return $operation->createMessage("Fetched timeslot successfully.", CrudOperation::NO_ERRORS, $timeslot);
    }

    public function getOrderedTimeslots() {
        $operation = new CrudOperation();

        try {
            $get_timeslots = $this->database->database_handle->prepare(
                "SELECT timeslot_start_time FROM timeslots
                ORDER BY timeslot_start_time ASC"
            );
            $gotten_timeslots = $get_timeslots->execute();
            if ($gotten_timeslots) {
                $timeslots = $get_timeslots->fetchAll();
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslots.", CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch timeslots.", CrudOperation::DATABASE_ERROR);
        }

        return $operation->createMessage("Fetched timeslots successfully.", CrudOperation::NO_ERRORS, $timeslots);
    }
}

class Booking
{
    private $database;
    private $session;

    public function __construct(Database $database, Session $session) {
        $this->database = $database;
        $this->session = $session;
    }

    public function createBooking($timeslot_start_time, $booking_date) {
        $operation = new CrudOperation();

        if (!isset($this->session->username)) {
            return $operation->createMessage(AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT);
        }

        $timeslot = new Timeslot($this->database);

        $existing_timeslot = $timeslot->getTimeslot($timeslot_start_time);
        if (isset($existing_timeslot->error)) {
            return $operation->createMessage($existing_timeslot->error, $existing_timeslot->error);
        }

        $unavailable_timeslots = $this->getUnavailableTimeslots($booking_date);
        $specific_timeslot_unavailable = FALSE;
        if (isset($unavailable_timeslots->error)) {
            return $operation->createMessage($unavailable_timeslots->error, $unavailable_timeslots->error);
        } else if (isset($unavailable_timeslots->result)) {
            foreach ($unavailable_timeslots->result as $unavailable_timeslot) {
                if ($unavailable_timeslot["timeslot_start_time"] == $timeslot_start_time) {
                    $specific_timeslot_unavailable = TRUE;
                    break;
                }
            }
        }

        $error = match(true) {
            $booking_date == "" => BookingError::BOOKING_DATE_EMPTY,
            $timeslot_start_time == "" => BookingError::TIMESLOT_START_TIME_EMPTY,
            DateTime::createFromFormat("Y-m-d", $booking_date) == FALSE => BookingError::BOOKING_DATE_INCORRECT_FORMAT,
            DateTime::createFromFormat("H:i:s", $timeslot_start_time) == FALSE => BookingError::TIMESLOT_INCORRECT_FORMAT,
            (strtotime($booking_date) < time()) AND ($booking_date != date("Y-m-d")) => BookingError::BOOKING_DATE_IN_PAST,
            time() > strtotime(date("$booking_date $timeslot_start_time")) => BookingError::TIMESLOT_IN_PAST,
            isset($existing_timeslot->result) ? FALSE : TRUE => BookingError::TIMESLOT_NOT_EXIST,
            $specific_timeslot_unavailable => BookingError::UNAVAILABLE_TIMESLOT,
            default => CrudOperation::NO_ERRORS
        };

        if ($error) {
            return $operation->createMessage($error->value, $error->value);
        }

        
        try {
            $create_booking = $this->database->database_handle->prepare(
                "INSERT INTO bookings
                    (timeslot_start_time, username, booking_date)
                VALUES
                    (:timeslot_start_time, :username, :booking_date)"
            );
            $created_booking = $create_booking->execute([
                "timeslot_start_time" => $timeslot_start_time,
                "username" => $this->session->username,
                "booking_date" => $booking_date
            ]);
            if (! $created_booking) {
                $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            } else {
                $operation->createMessage("Successfully created booking!");
            }
        } catch (PDOException $exception) {
            $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }

        return $operation;
    }

    public function getUnavailableTimeslots($booking_date) {
        $operation = new CrudOperation();

        try {
            $get_unavailable_timeslots = $this->database->database_handle->prepare(
                "SELECT COUNT(booking_id) AS number_of_tables_booked, timeslot_start_time FROM bookings
                WHERE booking_date = :booking_date GROUP BY timeslot_start_time
                HAVING number_of_tables_booked >= 10"
            );
            $gotten_unavailable_timeslots = $get_unavailable_timeslots->execute([
                "booking_date" => $booking_date
            ]);
            if ($gotten_unavailable_timeslots) {
                $unavailable_timeslots = $get_unavailable_timeslots->fetchAll();
                return $operation->createMessage("Fetched unavailable timeslots successfully.", CrudOperation::NO_ERRORS, $unavailable_timeslots);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch unavailable timeslots", CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch unavailable timeslots.", CrudOperation::DATABASE_ERROR);
        }
    }

    public function getBookings($username) {
        $operation = new CrudOperation();

        try {
            $get_bookings = $this->database->database_handle->prepare(
                "SELECT booking_id, timeslot_start_time, booking_date FROM bookings WHERE booking_date >= CURDATE() AND username = :username"
            );
            $gotten_bookings = $get_bookings->execute([
                "username" => $username
            ]);
            if ($gotten_bookings) {
                $bookings = $get_bookings->fetchAll();
                return $operation->createMessage("Successfully obtained bookings.", CrudOperation::NO_ERRORS, $bookings);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }

    public function getBooking($booking_id) {
        $operation = new CrudOperation();

        try {
            $get_bookings = $this->database->database_handle->prepare(
                "SELECT booking_id, timeslot_start_time, booking_date, username FROM bookings WHERE booking_id = :booking_id"
            );
            $gotten_bookings = $get_bookings->execute([
                "booking_id" => $booking_id
            ]);
            if ($gotten_bookings) {
                $bookings = $get_bookings->fetch();
                return $operation->createMessage("Successfully obtained bookings.", CrudOperation::NO_ERRORS, $bookings);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }
    
    public function cancelBooking($booking_id, $username) {
        $operation = new CrudOperation();

        try {
            $cancel_booking = $this->database->database_handle->prepare(
                "DELETE FROM bookings WHERE booking_id = :booking_id AND username = :username"
            );
            $cancelled_booking = $cancel_booking->execute([
                "booking_id" => $booking_id,
                "username" => $username
            ]);
            if ($cancelled_booking) {
                return $operation->createMessage("Successfully cancelled booking.");
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }
}