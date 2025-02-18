<?php

class CrudOperation
{
    public $message;
    public $error;
    public $result;

    public const IS_ERROR = TRUE;
    public const DATABASE_ERROR = "Failed to perform database operation.";
    public const NO_ERRORS = NULL;
    public const NO_RESULT = NULL;

    public function createMessage(string $message, $error = self::NO_ERRORS, $result = self::NO_RESULT) {
        $this->message = $message;
        if ($error) {
            $this->error = $error;
        }
        if ($result) {
            $this->result = $result;
        }

        return $this;
    }
}

require_once "models/session.php";
require_once "models/database.php";
require_once "models/account.php";

require_once "connect.php";

$session = Session::getInstance();
$account = new Account($database, $session);

function redirect($url, $alert = NULL, $error = NULL) {
    $url = WORKING_DIRECTORY."/$url";
    if ($alert) {
        $url .= "?alert=$alert";
        if ($error) {
            $url .= "&error=true";
        }
    }

    header("Location: $url");
    exit();
}