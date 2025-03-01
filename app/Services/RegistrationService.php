<?php

namespace App\Services;

use App\Models\User;
use Hash;
use Throwable;

class RegistrationService
{
    protected $user;
    protected $hash;

    public function __construct(User $user, Hash $hash)
    {
        $this->user = $user;
        $this->hash = $hash;
    }

    public function register($username, $password)
    {
        try {
            $this->user->create([
                "username" => $username,
                "password" => $this->hash::make($password)
            ]);
            return true;
        } catch (Throwable $throwable) {
            return false;
        }
    }
}