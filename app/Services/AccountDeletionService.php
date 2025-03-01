<?php

namespace App\Services;

use App\Models\User;
use Throwable;

class AccountDeletionService
{
    public function deleteAccount(?User $user) :bool
    {
        if ($user === NULL) {
            return false;
        }
        try {
            $user->delete();
            return true;
        } catch (Throwable $caught) {
            return false;
        }
    }
}