<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Enums\EtatEnum;
use Spatie\QueryBuilder\QueryBuilder;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAllClients(Request $request)
    {
        $comptes = $request->query('comptes');
        $active = $request->query('active');

        $query = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user']);

        if ($comptes !== null) {
            if ($comptes === 'oui') {
                $query->whereHas('user');
            } elseif ($comptes === 'non') {
                $query->whereDoesntHave('user');
            }
        }

        if ($active !== null) {
            $etat = $active === 'oui' ? EtatEnum::ACTIF->value : EtatEnum::INACTIF->value;
            $query->whereHas('user', function ($query) use ($etat) {
                $query->where('etat', $etat);
            });
        }

        return $query->get();
    }

    public function createClient(array $data)
    {
        return Client::create($data);
    }

    public function getClientById(string $id)
    {
        return Client::findOrFail($id);
    }

    public function updateClient(Client $client, array $data)
    {
        $client->update($data);
        return $client;
    }

    public function deleteClient(Client $client)
    {
        return $client->delete();
    }

    public function getClientByPhoneNumber(string $phoneNumber)
    {
        return Client::where('telephone', $phoneNumber)->firstOrFail();
    }

    public function getClientBySurname(string $surname)
    {
        return Client::where('surname', $surname)->firstOrFail();
    }
}