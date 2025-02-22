<?php

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

    public function orderItems($booking_id, $items_and_quantities) {
        $operation = new CrudOperation();

        $rows = [];
        foreach ($items_and_quantities as $item) {
            $rows[] = "(?, LAST_INSERT_ID(), ?)";
        }

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
}