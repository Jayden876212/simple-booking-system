<?php

class Session
{
    private const SESSION_STARTED = TRUE;
    private const SESSION_NOT_STARTED = FALSE;
    private $state = self::SESSION_NOT_STARTED;
    private static $instance;

    private function __construct() {}
    // We don't ever want to "create" a session unless prompted to
    // as an instance of session may already exist when we "create" the class.

    public static function getInstance() {
        if (! isset(self::$instance)) {
            self::$instance = new self;
        }

        self::$instance->start();

        return self::$instance;
    }

    public function start() {
        if ($this->state == self::SESSION_NOT_STARTED) {
            $this->state = session_start();
        }

        return $this->state;
    }

    public function destroy() {
        if ($this->state == self::SESSION_STARTED) {
            $this->state = !session_destroy();
            unset($_SESSION);

            return $this->state;
        }

        return FALSE;
    }

    public function __set(string $name, $value) {
        $_SESSION[$name] = $value;
    }

    public function __get(string $name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

    public function __isset(string $name) {
        return isset($_SESSION[$name]);
    }
}