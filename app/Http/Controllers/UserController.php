<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Facades\UserServiceFacade as UserService;
use App\Enums\EtatEnum;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(User::class, 'user');
    // }
    
    public function index(Request $request)
    {
        $users = UserService::getAllUsers($request);
        
        if ($users === null) {
            abort(404, 'Le rôle spécifié n\'existe pas');
        }
        
        return $users;
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:6',
            'role_id' => 'required|numeric|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        try {
            $user = UserService::createUser($data);
            return response($user, 201);
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $user = UserService::getUserById($id);
        if (!$user) {
            abort(404);
        }
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'string|max:255',
            'prenom' => 'string|max:255',
            'login' => 'string|unique:users,login,' . $user->id . '|max:255',
            'password' => 'string|min:6',
            'role_id' => 'numeric|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $updatedUser = UserService::updateUser($user, $data);
        return $updatedUser;
    }

    public function destroy(User $user)
    {
        UserService::deleteUser($user);
        return response(null, 204);
    }
}