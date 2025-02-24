<?php

class AccountError
{
    public const USERNAME_EMPTY = "You must enter a username. (Username was not received)";
    public const PASSWORD_EMPTY = "You must enter a password. (Password was not received)";
    public const USER_LOGGED_IN = "User needs to be logged out first.";
    public const USER_LOGGED_OUT = "You must log in first.";
}

enum SignUpError: string {
    case USERNAME_EXISTS = "Username already exists.";
    case USERNAME_EMPTY = AccountError::USERNAME_EMPTY;
    case PASSWORD_EMPTY = AccountError::PASSWORD_EMPTY;
    case USERNAME_TOO_LONG = "Username is too long!";
    case PASSWORD_TOO_LONG = "Password is too long!";
}

enum LoginError: string {
    case USERNAME_EMPTY = AccountError::USERNAME_EMPTY;
    case PASSWORD_EMPTY = AccountError::PASSWORD_EMPTY;
}

class Account
{
    private $database;
    private $session;
    public const MAX_USERNAME_LENGTH = 20;
    public const MAX_PASSWORD_LENGTH = 18;

    public function __construct(Database $database, Session $session) {
        $this->database = $database;
        $this->session = $session;
    }

    public function signUp(string $username, string $password) {
        $operation = new CrudOperation();
        if (!isset($this->session->username)) {
            $existing_user = self::getExistingUser($username);
            if (isset($existing_user->error)) {
                return $operation->createMessage($existing_user->message, $existing_user->error);
            }

            $username_length = strlen($username);
            $password_length = strlen($password);
            $user_exists = isset($existing_user->result) ? TRUE : FALSE;

            $error = match(true) {
                $user_exists => SignUpError::USERNAME_EXISTS,
                $username == "" => SignUpError::USERNAME_EMPTY,
                $password == "" => SignUpError::PASSWORD_EMPTY,
                $username_length > self::MAX_USERNAME_LENGTH => SignUpError::USERNAME_TOO_LONG,
                $password_length > self::MAX_PASSWORD_LENGTH => SignUpError::PASSWORD_TOO_LONG,
                default => CrudOperation::NO_ERRORS
            };

            if ($error) {
                return $operation->createMessage($error->value, $error->value);
            }


            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            try {
                $sign_up = $this->database->database_handle->prepare(
                    "INSERT INTO users (username, password) VALUES (:username, :password)"
                );
                $operation->result = $sign_up->execute([
                    "username" => $username,
                    "password" => $hashed_password
                ]);
                if ($operation->result) {
                    $operation->createMessage("Signed up successfully.");
                }
            } catch (PDOException $exception) {
                $operation->createMessage(CrudOperation::DATABASE_ERROR, $exception);
            }
        } else {
            $operation->createMessage(AccountError::USER_LOGGED_IN, AccountError::USER_LOGGED_IN);
        }
        return $operation;
    }

    public function logIn(string $username, string $password) {
        $operation = new CrudOperation();

        if (!isset($this->session->username)) {
            $error = match(true) {
                $username == "" => LoginError::USERNAME_EMPTY,
                $password == "" => LoginError::PASSWORD_EMPTY,
                default => CrudOperation::NO_ERRORS
            };

            if ($error) {
                $operation->createMessage($error->value, $error->value);

                return $operation;
            }

            try {
                $log_in = $this->database->database_handle->prepare(
                    "SELECT password FROM users WHERE username = :username"
                );
                $log_in->execute([
                    "username" => $username,
                ]);
                $user = $log_in->fetch(PDO::FETCH_OBJ);
                if ($user) {
                    $operation->result = $user;
                    if (password_verify($password, $user->password)) {
                        $this->session->username = $username;
                        $operation->createMessage("Logged in successfully.");
                    } else {
                        $operation->createMessage("Password is incorrect.", "Password is incorrect.");
                    }
                } else {
                    $operation->createMessage("User with that username does not exist.", "User with that username does not exist.");
                }
            } catch (PDOException $exception) {
                $operation->createMessage(CrudOperation::DATABASE_ERROR, $exception);
            }
        } else {
            $operation->createMessage(AccountError::USER_LOGGED_IN, AccountError::USER_LOGGED_IN);
        }

        return $operation;
    }

    private function getExistingUser(string $username) {
        $operation = new CrudOperation();

        try {
            $get_existing_user = $this->database->database_handle->prepare(
                "SELECT username FROM users WHERE username = :username"
            );
            $gotten_existing_user = $get_existing_user->execute([
                "username" => $username
            ]);
            if ($gotten_existing_user) {
                $user = $get_existing_user->fetch();
                $operation->createMessage("User Retrieved!", CrudOperation::NO_ERRORS, $user);
            } else {
                $operation->createMessage(CrudOperation::DATABASE_ERROR, $gotten_existing_user, CrudOperation::NO_RESULT);
            }
        } catch (PDOException $exception) {
            $operation->createMessage(CrudOperation::DATABASE_ERROR, $exception, CrudOperation::NO_RESULT);
        }

        return $operation;
    }

    public function logOut() {
        if (isset($this->session->username)) {
            $this->session->destroy();
        }
    }

    public function delete() {
        $operation = new CrudOperation();

        if (isset($this->session->username)) {
            try {
                $delete_user = $this->database->database_handle->prepare(
                    "DELETE FROM users WHERE username = :username"
                );
                $deleted_user = $delete_user->execute([
                    "username" => $this->session->username
                ]);
                if ($deleted_user) {
                    self::logOut();
                    $operation->createMessage("Successfully deleted user and logged out.");
                } else {
                    $operation->createMessage(CrudOperation::DATABASE_ERROR, $deleted_user);
                }
            } catch (PDOException $exception) {
                $operation->createMessage(CrudOperation::DATABASE_ERROR, $exception);
            }
        } else {
            $operation->createMessage(AccountError::USER_LOGGED_OUT, AccountError::USER_LOGGED_OUT);
        }
        return $operation;
    }
}