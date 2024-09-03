<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use App\Http\Resources\DetteResource;
use App\Http\Requests\StoreDetteRequest;

class DetteController extends Controller
{
    use RestResponseTrait;

    public function __construct()
    {
        $this->authorizeResource(Dette::class, 'dette');
    }

    public function getDettesByClient($clientId)
    {
        $client = Client::find($clientId);
        if (!$client) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Client non trouvé', 404);
        }

        $dettes = Dette::where('client_id', $clientId)->get();

        return $this->sendResponse($dettes, StateEnum::SUCCESS, 'Dettes récupérées avec succès');
    }

    public function store(StoreDetteRequest $request)
    {
        $validatedData = $request->validated();

        $dette = Dette::create([
            'date' => $validatedData['date'],
            'montant' => $validatedData['montant'],
            'montantDu' => $validatedData['montantDu'],
            'montantRestant' => $validatedData['montantRestant'],
            'client_id' => $validatedData['client_id'],
        ]);

        foreach ($validatedData['articles'] as $articleData) {
            $article = Article::find($articleData['id']);
            $dette->articles()->attach($article, [
                'qteVente' => $articleData['qteVente'],
                'prixVente' => $articleData['prixVente'],
            ]);

            $article->qteStock -= $articleData['qteVente'];
            $article->save();
        }

        return $this->sendResponse(new DetteResource($dette), StateEnum::SUCCESS, 'Dette créée avec succès', 201);
    }
}
