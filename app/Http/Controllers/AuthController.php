<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\StateEnum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BlacklistedToken;
use App\Interfaces\AuthentificationServiceInterface;

class AuthController extends Controller
{
    //Dépendance Injection 
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $result = $this->authService->authenticate($request->only('login', 'password'));

        if ($result) {
            return $this->sendResponse($result, StateEnum::SUCCESS, 'Connexion réussie');
        }

        return $this->sendResponse(null, StateEnum::ECHEC, 'Les identifiants sont incorrects', 401);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);
    
        $user = User::where('refresh_token', $request->refresh_token)->first();
        if (!$user) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Refresh token invalide', 401);
        }
    
        // Ajouter l'ancien refresh token à la liste noire
        BlacklistedToken::create(['token' => $request->refresh_token, 'type' => 'refresh']);
    
        // Révoquer les anciens tokens
        $this->authService->revokeTokens($user);
        $tokens = $this->authService->generateTokens($user);
        return $this->sendResponse($tokens, StateEnum::SUCCESS, 'Token rafraîchi avec succès');
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token) {
            BlacklistedToken::create([
                'token' => $token,
                'type' => 'access',
                'revoked_at' => now(),
            ]);
        }

        $this->authService->logout();
        return $this->sendResponse(null, StateEnum::SUCCESS, 'Déconnexion réussie');
    }

    private function sendResponse($data, $status, $message, $httpStatus = 200)
    {
        return response()->json([
            'data'    => $data,
            'status'  => $status,
            'message' => $message,
        ], $httpStatus);
    }
}