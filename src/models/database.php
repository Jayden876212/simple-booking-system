<?php

class Database
{

    private $driver;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public $database_handle;

    public function __construct(array $settings)
    {
        $this->driver = $settings["driver"];
        $this->host = $settings["host"];
        $this->dbname = $settings["dbname"];
        $this->username = $settings["username"];
        $this->password = $settings["password"];
        $this->database_handle = new PDO (
            "$this->driver:host=$this->host;dbname=$this->dbname",
            $this->username,
            $this->password
        );
    }
}
