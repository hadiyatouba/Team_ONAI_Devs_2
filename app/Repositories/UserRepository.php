<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Role;
use App\Enums\EtatEnum;
use Illuminate\Http\Request;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(Request $request)
    {
        $active = $request->query('active');
        $role = $request->query('role');

        $query = User::query();

        if ($active !== null) {
            $etat = $active === 'oui' ? EtatEnum::ACTIF->value : EtatEnum::INACTIF->value;
            $query->where('etat', $etat);
        }

        if ($role !== null) {
            $roleExists = Role::where('name', $role)->exists();

            if (!$roleExists) {
                return null;
            }

            $query->whereHas('role', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        return $query->get();
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function getUserById(int $id)
    {
        return User::findOrFail($id);
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
        return $user->delete();
    }
}