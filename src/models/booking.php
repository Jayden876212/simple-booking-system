<?php

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
}

class Booking
{
    private $database;
    private $session;
    private $account;

    public function __construct(Database $database, Session $session, Account $account) {
        $this->database = $database;
        $this->session = $session;
        $this->account = $account;
    }

    public function createBooking($timeslot_start_time, $booking_date) {
        $operation = new CrudOperation();

        if (!isset($this->session->username)) {
            return $operation->createMessage(AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT);
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
                HAVING number_of_tables_booked >= 2"
            );
            $gotten_unavailable_timeslots = $get_unavailable_timeslots->execute([
                "booking_date" => $booking_date
            ]);
            if ($gotten_unavailable_timeslots) {
                $unavailable_timeslots = $get_unavailable_timeslots->fetchAll();
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch unavailable timeslots", CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR . "Failed to fetch unavailable timeslots.", CrudOperation::DATABASE_ERROR);
        }

        return $operation->createMessage("Fetched unavailable timeslots successfully.", CrudOperation::NO_ERRORS, $unavailable_timeslots);
    }
}