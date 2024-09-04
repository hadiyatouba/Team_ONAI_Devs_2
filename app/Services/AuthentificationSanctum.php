<?php

namespace App\Services;

use Laravel\Sanctum\NewAccessToken;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AuthentificationServiceInterface;

class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('auth_token');
            
            $token = $tokenResult instanceof NewAccessToken 
                ? $tokenResult->plainTextToken 
                : ($tokenResult->accessToken ?? $tokenResult);

            return [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
        }
        return null;
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }
    }

    public function revokeTokens(User $user)
    {
        foreach ($user->tokens as $token) {
            $token->delete();
        }
    }

    public function generateTokens(User $user)
    {
        $tokenResult = $user->createToken('auth_token');
        
        $token = $tokenResult instanceof NewAccessToken 
            ? $tokenResult->plainTextToken 
            : ($tokenResult->accessToken ?? $tokenResult);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
