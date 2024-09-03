<?php

// app/Models/Article.php

namespace App\Models;

use App\Models\Scopes\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['libelle', 'description', 'price', 'stock', 'etat'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new FilterScope());
    }

    public static function findByLibelle(string $libelle): ?self
    {
        return self::where('libelle', 'like', "%$libelle%")->first();
    }

    // Scope pour état
    public function scopeEtat($query, $etat)
    {
        return $query->where('etat', $etat);
    }
}

