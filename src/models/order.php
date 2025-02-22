<?php

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