<?php

namespace App\Interfaces;

use Illuminate\Foundation\Auth\User;

interface AuthentificationServiceInterface
{
    public function authenticate(array $credentials);
    // public function revokeTokens(User $user);
    // public function generateTokens(User $user);
    public function logout();
}