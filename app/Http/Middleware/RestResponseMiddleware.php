<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateEnum;

class RestResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$response instanceof Response) {
            $response = response()->json($response);
        }

        $statusCode = $response->getStatusCode();
        $originalContent = $response->getContent();

        // Déterminer le statut en fonction du code HTTP
        $status = $statusCode < 400 ? StateEnum::SUCCESS : StateEnum::ECHEC;

        // Extraire les données
        $data = json_decode($originalContent, true);

        // Construire la nouvelle structure de réponse
        $formattedResponse = [
            'data' => $data,
            'status' => $status->value,
            'message' => $this->getDefaultMessageForStatusCode($statusCode),
        ];

        // Remplacer le contenu de la réponse
        $response->setContent(json_encode($formattedResponse));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function getDefaultMessageForStatusCode(int $statusCode): string
    {
        return match ($statusCode) {
            200 => 'Opération réussie',
            201 => 'Ressource créée',
            204 => 'Pas de contenu',
            400 => 'Requête incorrecte',
            401 => 'Non autorisé',
            403 => 'Interdit',
            404 => 'Non trouvé',
            422 => 'Entité non traitable',
            500 => 'Erreur interne du serveur',
            default => 'Une erreur est survenue',
        };
    }
}