<?php

enum OrderError: string {
    case BOOKING_ID_EMPTY = "You must select a booking ID.";
    case AT_LEAST_ONE_ITEM = "You must purchase at least one item.";
    case BOOKING_ID_NOT_EXIST = "A booking with that ID does not exist.";
    case ITEM_NAMES_NOT_EXIST = "An item with that name does not exist.";
    case USER_NO_PERMISSION = "The selected booking belongs to another user (booking username must be the same as the one in the session).";
    case ITEM_QUANTITY_ZERO_OR_LESS = "The quantity of the selected items must be at least 1.";
    case BOOKING_NOT_IN_TIMESLOT = "Your order must be done at the time between your timeslot's start time and the next timeslot's start time.";
}

class Order
{
    private $database;
    private $session;
    private $booking;

    public function __construct(Database $database, Session $session) {
        $this->database = $database;
        $this->session = $session;
        $this->booking = new Booking($database, $session);
    }

    private function createRows($items_and_quantities) {
        $rows = [];
        foreach ($items_and_quantities as $item) {
            $rows[] = "(?, LAST_INSERT_ID(), ?)";
        }

        return $rows;
    }

    public function orderItems($booking_id, $items_and_quantities) {
        $operation = new CrudOperation();

        $items_are_found = false;
        if (isset($items_and_quantities)) {
            foreach ($items_and_quantities as $name => $quantity) {
                if ($name AND $quantity) {
                    $items_are_found = true;
                }
            }
        }

        $booking = $this->booking->getBooking($booking_id);
        if (isset($booking->error)) {
            return $operation->createMessage($booking->message, $booking->message);
        }

        $items_exist = true;
        $quantity_less_than_zero = false;
        if ($items_are_found) {
            $item_model = new Item($this->database);
            foreach ($items_and_quantities as $name => $quantity) {
                $item = $item_model->getItem($name);
                if (isset($item->error)) {
                    return $operation->createMessage($item->message, $item->message);
                }

                if (! $item->result) {
                    $items_exist = false;
                    break;
                }
            }

            $items_to_be_removed = [];
            foreach ($items_and_quantities as $name => $quantity) {
                if ($quantity < 0) {
                    $quantity_less_than_zero = true;
                } else if ($quantity == 0) {
                    $items_to_be_removed[] = $name;
                }
            }
            foreach ($items_to_be_removed as $item_name) {
                unset($items_and_quantities[$item_name]);
            }
        }

        $timeslot_model = new Timeslot($this->database);
        $ordered_timeslots = $timeslot_model->getOrderedTimeslots();
        if (isset($ordered_timeslots->error)) {
            return $operation->createMessage($ordered_timeslots->message, $ordered_timeslots->message);
        }

        $timeslot_start_times = array_keys($ordered_timeslots->result);
        $start_time_key = array_search($booking->result["timeslot_start_time"], $timeslot_start_times);

        $valid_start_time = strtotime($booking->result["booking_date"] . " " . $booking->result["timeslot_start_time"]);
        $valid_end_time = strtotime($booking->result["booking_date"] . " " . $ordered_timeslots->result[$start_time_key + 1]["timeslot_start_time"]);

        $error = match(true) {
            ($booking_id == NULL) OR ($booking_id == 0) OR ($booking_id == "") => OrderError::BOOKING_ID_EMPTY,
            ! $items_are_found => OrderError::AT_LEAST_ONE_ITEM,
            ! $booking->result => OrderError::BOOKING_ID_NOT_EXIST,
            ! $items_exist => OrderError::ITEM_NAMES_NOT_EXIST,
            $booking->result["username"] != $this->session->username => OrderError::USER_NO_PERMISSION,
            $quantity_less_than_zero => OrderError::ITEM_QUANTITY_ZERO_OR_LESS,
            // (time() < $valid_start_time) OR (time() >= $valid_end_time) => OrderError::BOOKING_NOT_IN_TIMESLOT,
            default => false
        };

        if ($error) {
            return $operation->createMessage($error->value, $error->value);
        }

        $rows = self::createRows($items_and_quantities);

        try {
            $order_items = $this->database->database_handle->prepare(
                "INSERT INTO orders (booking_id, datetime_ordered) VALUES (?, NOW());
                INSERT INTO item_orders (item_name, order_id, quantity) VALUES
                " . implode(",", $rows)
            );
            $index = 1;
            $order_items->bindParam($index, $booking_id, PDO::PARAM_INT);
            foreach ($items_and_quantities as $item_name => $item_quantity) {
                $index += 1;
                $order_items->bindValue($index, $item_name, PDO::PARAM_STR);
                $index += 1;
                $order_items->bindValue($index, $item_quantity, PDO::PARAM_INT);
            }
            $ordered_items = $order_items->execute();
            if ($ordered_items) {
                return $operation->createMessage("Ordered items successfully.");
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }

    public function getOrders($username) {
        $operation = new CrudOperation();

        try {
            $get_orders = $this->database->database_handle->prepare(
                "SELECT orders.order_id, orders.datetime_ordered, SUM(item_orders.quantity * items.price) AS total_price
                FROM orders
                JOIN item_orders ON orders.order_id = item_orders.order_id
                JOIN bookings ON bookings.booking_id = orders.booking_id
                JOIN items ON items.item_name = item_orders.item_name
                WHERE bookings.username = :username
                GROUP BY orders.order_id"
            );
            $gotten_orders = $get_orders->execute([
                "username" => $username
            ]);
            if ($gotten_orders) {
                $orders = $get_orders->fetchAll();
                return $operation->createMessage("Successfully obtained orders.", CrudOperation::NO_ERRORS, $orders);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }

    public function getOrderItems($username, $order_id) {
        $operation = new CrudOperation();

        try {
            $get_order = $this->database->database_handle->prepare(
                "SELECT item_orders.item_name, SUM(item_orders.quantity), SUM(items.price) 
                FROM orders
                JOIN item_orders ON orders.order_id = item_orders.order_id
                JOIN bookings ON bookings.booking_id = orders.booking_id
                JOIN items ON items.item_name = item_orders.item_name
                WHERE (bookings.username = :username) AND (orders.order_id = :order_id)
                GROUP BY item_orders.item_name"
            );
            $gotten_order = $get_order->execute([
                "username" => $username,
                "order_id" => $order_id
            ]);
            if ($gotten_order) {
                $order = $get_order->fetchAll();
                return $operation->createMessage("Successfully obtained order and associated items.", CrudOperation::NO_ERRORS, $order);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }
}


class Item
{
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function getItems() {
        $operation = new CrudOperation();

        try {
            $get_items = $this->database->database_handle->prepare(
                "SELECT item_name, price FROM items"
            );
            $gotten_items = $get_items->execute();
            if ($gotten_items) {
                $items = $get_items->fetchAll();
                return $operation->createMessage("Successfully obtained items.", CrudOperation::NO_ERRORS, $items);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }

    public function getItem($item_name) {
        $operation = new CrudOperation();

        try {
            $get_item = $this->database->database_handle->prepare(
                "SELECT item_name, price FROM items WHERE item_name = :item_name"
            );
            $gotten_item = $get_item->execute([
                "item_name" => $item_name
            ]);
            if ($gotten_item) {
                $item = $get_item->fetch();
                return $operation->createMessage("Successfully obtained item.", CrudOperation::NO_ERRORS, $item);
            } else {
                return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
            }
        } catch (PDOException $exception) {
            return $operation->createMessage(CrudOperation::DATABASE_ERROR, CrudOperation::DATABASE_ERROR);
        }
    }
}