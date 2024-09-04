<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\ArticleServiceInterface;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
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
        
        return $articles;
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->articleService->create($request->validated());
        return response($article, 201);
    }

    public function show(int $id)
    {
        $article = $this->articleService->find($id);
        if (!$article) {
            abort(404);
        }
        return $article;
    }

    public function update(UpdateArticleRequest $request, int $id)
    {
        $article = $this->articleService->update($id, $request->validated());
        if (!$article) {
            abort(404);
        }
        return $article;
    }

    public function destroy(int $id)
    {
        $deleted = $this->articleService->delete($id);
        if (!$deleted) {
            abort(404);
        }
        return response(null, 204);
    }

    public function getByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');
        if (empty($libelle)) {
            abort(400, 'Le libelle est requis');
        }

        $article = $this->articleService->findByLibelle($libelle);
        if (!$article) {
            abort(404);
        }

        return $article;
    }

    public function updateMultiple(Request $request)
    {
        $result = $this->articleService->updateMultiple($request->articles);
        if (!empty($result['failed_updates'])) {
            abort(422, 'Certaines mises à jour ont échoué');
        }
        return $result;
    }

    public function trashed()
    {
        return $this->articleService->trashed();
    }

    public function restore($id)
    {
        $article = $this->articleService->restore($id);
        if (!$article) {
            abort(404, 'Article non trouvé ou déjà restauré');
        }
        return $article;
    }

    public function forceDelete($id)
    {
        $deleted = $this->articleService->forceDelete($id);
        if (!$deleted) {
            abort(404);
        }
        return response(null, 204);
    }
}