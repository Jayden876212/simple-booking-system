<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Guard;

class AuthenticationService
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function authenticate(string $username, string $password) :bool
    {
        $credentials = [
            "username" => $username,
            "password" => $password
        ];
        if ($this->auth->attempt($credentials)) {
            return true;
        } else {
            return false;
        }
    }
}