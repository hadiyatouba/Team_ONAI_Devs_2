<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Enums\EtatEnum;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
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
        // Utilisation d'un scope pour récupérer les clients actifs
        $clients = Client::active()->get();

        return ResponseHelper::sendOk(new ClientCollection($clients), 'Liste des clients récupérée avec succès');
    }

    // public function index(Request $request)
    // {
    //     $clients = ClientService::getAllClients($request);
    //     return ResponseHelper::sendOk(new ClientCollection($clients), 'Liste des clients récupérée avec succès');
    // }

    public function getByPhoneNumber(Request $request)
    {
        $telephone = $request->input('telephone');

        // Utilisation du scope byPhoneNumber
        $client = Client::withoutGlobalScope('filtreParTelephone')
        ->where('telephone', $telephone)
        ->first();

        if (!$client) {
            return ResponseHelper::sendNotFound('Client non trouvé avec ce numéro de téléphone');
        }

        return ResponseHelper::sendOk(new ClientResource($client), 'Client récupéré avec succès');
    }

    // public function getByPhoneNumber(Request $request)
    // {
    //     $request->validate(['telephone' => 'required|string']);
    //     try {
    //         $client = ClientService::getClientByPhoneNumber($request->telephone);
    //         return ResponseHelper::sendOk(new ClientResource($client), 'Client récupéré avec succès');
    //     } catch (\Exception $e) {
    //         return ResponseHelper::sendNotFound('Client non trouvé avec ce numéro de téléphone');
    //     }
    // }


    public function store(StoreClientRequest $request)
    {
        try {
            $client = ClientService::createClient($request->validated());
            return ResponseHelper::sendCreated(new ClientResource($client), 'Client créé avec succès');
        } catch (\Exception $e) {
            return ResponseHelper::sendServerError('Erreur lors de la création du client : ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $client = ClientService::getClientById($id);
        return ResponseHelper::sendOk(new ClientResource($client), 'Client récupéré avec succès');
    }

    public function update(Request $request, Client $client)
    {
        $updatedClient = ClientService::updateClient($client, $request->validated());
        return ResponseHelper::sendOk(new ClientResource($updatedClient), 'Client mis à jour avec succès');
    }

    public function destroy(Client $client)
    {
        ClientService::deleteClient($client);
        return ResponseHelper::sendOk(null, 'Client supprimé avec succès');
    }


    public function addAccount(Request $request)
    {
        $request->validate([
            'surname' => 'required|string|exists:clients,surname',
            'user.nom' => 'required|string|max:255',
            'user.prenom' => 'required|string|max:255',
            'user.login' => 'required|string|unique:users,login|max:255',
            'user.password' => 'required|string|min:6|confirmed',
            'user.role_id' => 'required|exists:roles,id',
            'user.etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
        ]);

        try {
            $client = ClientService::addAccountToClient($request->validated());
            return ResponseHelper::sendCreated(new ClientResource($client), 'Compte ajouté au client avec succès');
        } catch (\Exception $e) {
            return ResponseHelper::sendServerError('Erreur lors de l\'ajout du compte au client : ' . $e->getMessage());
        }
    }
}
