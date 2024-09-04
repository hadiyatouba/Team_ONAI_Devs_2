<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Enums\EtatEnum;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;
use App\Http\Requests\StoreClientRequest;
use App\Facades\ClientServiceFacade as ClientService;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request)
    {
        $clients = ClientService::getAllClients($request);
        return new ClientCollection($clients);
    }

    public function store(StoreClientRequest $request)
    {
        try {
            $client = ClientService::createClient($request->validated());
            return new ClientResource($client);
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la création du client : ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $client = ClientService::getClientById($id);
        return new ClientResource($client);
    }

    public function update(Request $request, Client $client)
    {
        $updatedClient = ClientService::updateClient($client, $request->validated());
        return new ClientResource($updatedClient);
    }

    public function destroy(Client $client)
    {
        ClientService::deleteClient($client);
        return response(null, 204);
    }

    public function getByPhoneNumber(Request $request)
    {
        $request->validate(['telephone' => 'required|string']);
        try {
            $client = ClientService::getClientByPhoneNumber($request->telephone);
            return new ClientResource($client);
        } catch (\Exception $e) {
            abort(404, 'Client non trouvé avec ce numéro de téléphone');
        }
    }

    public function addAccount(Request $request)
    {
        $validatedData = $request->validate([
            'surname' => 'required|string|exists:clients,surname',
            'user.nom' => 'required|string|max:255',
            'user.prenom' => 'required|string|max:255',
            'user.login' => 'required|string|unique:users,login|max:255',
            'user.password' => 'required|string|min:6|confirmed',
            'user.role_id' => 'required|exists:roles,id',
            'user.etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
        ]);

        try {
            $client = ClientService::addAccountToClient($validatedData);
            return new ClientResource($client);
        } catch (\Exception $e) {
            abort(500, 'Erreur lors de l\'ajout du compte au client : ' . $e->getMessage());
        }
    }
}