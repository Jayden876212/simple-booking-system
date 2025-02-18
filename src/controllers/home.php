<?php
    class HomeController {
        private $session;
        private $account;

        public function __construct(Session $session, Account $account) {
            $this->session = $session;
            $this->account = $account;
        }
        
        public function handleRequest() {
            include_once "include/utils.php";

            require "views/home.php";
            exit();
        }
    }