<?php

namespace App\Services;

use App\Services\Interfaces\ClientServiceInterface;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\EtatEnum;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAllClients(Request $request)
    {
        return $this->clientRepository->getAllClients($request);
    }

    public function createClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = $this->clientRepository->createClient($data);

            if (isset($data['user'])) {
                $userData = $data['user'];
                $userData['password'] = Hash::make($userData['password']);
                $userData['etat'] = $userData['etat'] ?? EtatEnum::ACTIF->value;

                $user = User::create($userData);
                $client->user()->associate($user);
                $client->save();
            }

            return $client;
        });
    }

    public function getClientById(string $id)
    {
        return $this->clientRepository->getClientById($id);
    }

    public function updateClient(Client $client, array $data)
    {
        return $this->clientRepository->updateClient($client, $data);
    }

    public function deleteClient(Client $client)
    {
        return $this->clientRepository->deleteClient($client);
    }

    public function getClientByPhoneNumber(string $phoneNumber)
    {
        return $this->clientRepository->getClientByPhoneNumber($phoneNumber);
    }

    public function addAccountToClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = $this->clientRepository->getClientBySurname($data['surname']);

            if ($client->user()->exists()) {
                throw new \Exception('Ce client a déjà un compte utilisateur associé.');
            }

            $userData = $data['user'];
            $userData['password'] = Hash::make($userData['password']);

            $user = User::create($userData);
            $client->user()->associate($user);
            $client->save();

            return $client;
        });
    }
}