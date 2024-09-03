<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $articles = [
            ['libelle' => 'T-shirt Sénégalais', 'description' => 'T-shirt avec motif sénégalais', 'price' => 2000, 'stock' => 50],
            ['libelle' => 'Veste Dakar', 'description' => 'Veste de la ville de Dakar', 'price' => 5000, 'stock' => 30],
            ['libelle' => 'Casquette Touba', 'description' => 'Casquette de Touba', 'price' => 1500, 'stock' => 20],
            ['libelle' => 'Baguette de pain', 'description' => 'Baguette fraîche', 'price' => 500, 'stock' => 100],
            ['libelle' => 'Riz Sénégalais', 'description' => 'Riz traditionnel sénégalais', 'price' => 3000, 'stock' => 80],
            // Ajoutez d'autres articles spécifiques ici
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }
    }
}
