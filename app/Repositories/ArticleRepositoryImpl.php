<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepositoryImpl implements ArticleRepositoryInterface
{
    public function all(): Collection
    {
        return Article::all();
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function find(int $id): ?Article
    {
        return Article::find($id);
    }

    public function update(int $id, array $data): ?Article
    {
        $article = $this->find($id);
        if (!$article) {
            return null;
        }
        $article->update($data);
        return $article;
    }

    public function delete(int $id): bool
    {
        $article = $this->find($id);
        if (!$article) {
            return false;
        }
        return $article->delete();
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return Article::findByLibelle($libelle);
    }

    public function findByEtat(string $etat): Collection
    {
        return Article::where('etat', $etat)->get();
    }

    public function trashed(): Collection
    {
        return Article::onlyTrashed()->get();
    }

    public function restore(int $id): ?Article
    {
        $article = Article::withTrashed()->find($id);
        if (!$article) {
            return null;
        }
        $article->restore();
        return $article;
    }

    public function forceDelete(int $id): bool
    {
        $article = Article::withTrashed()->find($id);
        if (!$article) {
            return false;
        }
        return $article->forceDelete();
    }
}