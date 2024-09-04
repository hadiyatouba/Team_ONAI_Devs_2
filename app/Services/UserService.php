<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Nyholm\Psr7\UploadedFile;
use Illuminate\Support\Facades\Hash;
use App\Facades\UploadFacade as Upload;
use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $id)
    {
        $user = $this->userRepository->getUserById($id);
        if ($user && $user->photo) {
            $user->photo_base64 = Upload::getBase64Photo($user->photo);
        }
        return $user;
    }

    public function getAllUsers(Request $request)
    {
        $users = $this->userRepository->getAllUsers($request);
        foreach ($users as $user) {
            if ($user->photo) {
                $user->photo_base64 = Upload::getBase64Photo($user->photo);
            }
        }
        return $users;
    }
    public function createUser(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = Upload::uploadPhoto($data['photo'], 'users');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->createUser($data);
    }

    // public function getUserById(int $id)
    // {
    //     return $this->userRepository->getUserById($id);
    // }

    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->updateUser($user, $data);
    }

    public function deleteUser(User $user)
    {
        return $this->userRepository->deleteUser($user);
    }
}
