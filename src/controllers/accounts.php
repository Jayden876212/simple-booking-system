<?php

class AccountDeletionController
{
    private $session;
    private $account;

    public function __construct(Session $session, Account $account) {
        $this->session = $session;
        $this->account = $account;
    }

    public function handleRequest() {
        require_once "include/utils.php";

        if (! isset($this->session->username)) {
            redirect("account/login", AccountError::USER_LOGGED_OUT, CrudOperation::IS_ERROR);
        }

        $deleted_account = $this->account->delete();
        if (isset($deleted_account->error)) {
            redirect("home", $deleted_account->message, CrudOperation::IS_ERROR);
        } else {
            redirect("home", $deleted_account->message);
        }
    }
}
class LoginController {
    private $session;
    private $account;

    public function __construct(Session $session, Account $account) {
        $this->session = $session;
        $this->account = $account;
    }
    
    public function handleRequest() {
        require_once "include/utils.php";

        $set_username = $this->session->username ?? FALSE;
        if ($set_username) {
            redirect("home", AccountError::USER_LOGGED_IN, CrudOperation::IS_ERROR);
        }

        $login = $_POST["login"] ?? FALSE;
        $username = $_POST["username"] ?? FALSE;
        $password = $_POST["password"] ?? FALSE;
        $form_submitted = $login AND $username AND $password;
        if ($form_submitted) {
            $logged_in = $this->account->logIn($username, $password);
            if (! $logged_in->error) {
                $target_page = $_POST["target_page"] ?? FALSE;
                if ($target_page) {
                    redirect($target_page, $logged_in->message);
                } else {
                    redirect("home", $logged_in->message);
                }
            } else {
                redirect("account/login", $logged_in->message, CrudOperation::IS_ERROR);
            }
        }

        require "views/login.php";
        exit();
    }
}

class LogoutController {
    private $session;
    private $account;

    public function __construct(Session $session, Account $account) {
        $this->session = $session;
        $this->account = $account;
    }
    
    public function handleRequest() {
        require_once "include/utils.php";

        if (! isset($this->session->username)) {
            redirect("home", AccountError::USER_LOGGED_OUT, CrudOperation::IS_ERROR);
        }

        $this->account->logOut();
        redirect("home", "User successfully logged out");
    }
}

class RegistrationController {
    private $session;
    private $account;

    public function __construct(Session $session, Account $account) {
        $this->session = $session;
        $this->account = $account;
    }

    public function handleRequest() {
        require_once "include/utils.php";

        if (isset($this->session->username)) {
            redirect("home");
        }

        $sign_up = $_POST["sign_up"] ?? FALSE;
        $username = $_POST["username"] ?? FALSE;
        $password = $_POST["password"] ?? FALSE;
        $form_submitted = $sign_up AND $username AND $password;
        if ($form_submitted) {
            $signed_up = $this->account->signUp($username, $password);
            if (! $signed_up->error) {
                redirect("account/login", $signed_up->message);
            } else {
                redirect("account/register", $signed_up->message, CrudOperation::IS_ERROR);
            }
        }

        require "views/register.php";
        exit();
    }
}