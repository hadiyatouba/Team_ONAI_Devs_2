<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ArticleServiceImpl implements ArticleServiceInterface
{
    private $repository;

    public function __construct(ArticleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Article
    {
        return $this->repository->create($data);
    }

    public function find(int $id): ?Article
    {
        return $this->repository->find($id);
    }

    public function update(int $id, array $data): ?Article
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function findByLibelle(string $libelle): ?Article
    {
        return $this->repository->findByLibelle($libelle);
    }

    public function findByEtat(string $etat): Collection
    {
        return $this->repository->findByEtat($etat);
    }

    public function updateMultiple(array $articles): array
    {
        $updatedArticles = [];
        $failedUpdates = [];

        DB::beginTransaction();

        try {
            foreach ($articles as $articleData) {
                $article = $this->repository->find($articleData['id']);
                if (!$article) {
                    throw new \Exception("Article with ID {$articleData['id']} not found");
                }

                $updatedArticle = $this->repository->update($article->id, $articleData);
                if ($updatedArticle) {
                    $updatedArticles[] = $updatedArticle;
                } else {
                    $failedUpdates[] = $articleData;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'updated_articles' => $updatedArticles,
            'failed_updates' => $failedUpdates
        ];
    }

    public function trashed(): Collection
    {
        return $this->repository->trashed();
    }

    public function restore(int $id): ?Article
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int $id): bool
    {
        return $this->repository->forceDelete($id);
    }
}