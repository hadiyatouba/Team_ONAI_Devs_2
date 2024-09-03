<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Enums\StateEnum;
use Illuminate\Http\Request;
use App\Traits\RestResponseTrait;
use App\Services\ArticleServiceInterface;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    use RestResponseTrait;

    private $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
        $this->authorizeResource(Article::class, 'Article');
    }

    public function index(Request $request)
    {
        $disponible = $request->query('disponible');
        $articles = $this->articleService->all();

        if ($disponible !== null) {
            $articles = $articles->filter(function ($article) use ($disponible) {
                return $disponible === 'oui' ? $article->stock > 0 : $article->stock == 0;
            });
        }

        return $this->sendResponse($articles, StateEnum::SUCCESS, 'Articles récupérés avec succès');
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->articleService->create($request->validated());
        return $this->sendResponse($article, StateEnum::SUCCESS, 'Article créé avec succès', 201);
    }

    public function show(int $id)
    {
        $article = $this->articleService->find($id);
        if (!$article) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé', 404);
        }
        return $this->sendResponse($article, StateEnum::SUCCESS, 'Article récupéré avec succès');
    }

    public function update(UpdateArticleRequest $request, int $id)
    {
        $article = $this->articleService->update($id, $request->validated());
        if (!$article) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé', 404);
        }
        return $this->sendResponse($article, StateEnum::SUCCESS, 'Article mis à jour avec succès');
    }

    public function destroy(int $id)
    {
        $deleted = $this->articleService->delete($id);
        if (!$deleted) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé', 404);
        }
        return $this->sendResponse(null, StateEnum::SUCCESS, 'Article supprimé avec succès');
    }

    public function getByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');
        if (empty($libelle)) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Le libelle est requis', 422);
        }

        $article = $this->articleService->findByLibelle($libelle);
        if (!$article) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé', 404);
        }

        return $this->sendResponse($article, StateEnum::SUCCESS, 'Article récupéré avec succès');
    }

    public function updateMultiple(Request $request)
    {
        $result = $this->articleService->updateMultiple($request->articles);
        $status = empty($result['failed_updates']) ? StateEnum::SUCCESS : StateEnum::ECHEC;
        $message = empty($result['failed_updates']) ? 'Tous les articles ont été mis à jour avec succès' : 'Certaines mises à jour ont échoué';
        $httpStatus = empty($result['failed_updates']) ? 200 : 422;

        return $this->sendResponse($result, $status, $message, $httpStatus);
    }

    public function trashed()
    {
        $trashedArticles = $this->articleService->trashed();
        return $this->sendResponse($trashedArticles, StateEnum::SUCCESS, 'Articles supprimés récupérés avec succès');
    }

    public function restore($id)
    {
        $article = $this->articleService->restore($id);
        if (!$article) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé ou déjà restauré', 404);
        }
        return $this->sendResponse($article, StateEnum::SUCCESS, 'Article restauré avec succès');
    }

    public function forceDelete($id)
    {
        $deleted = $this->articleService->forceDelete($id);
        if (!$deleted) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'Article non trouvé', 404);
        }
        return $this->sendResponse(null, StateEnum::SUCCESS, 'Article supprimé définitivement');
    }
}