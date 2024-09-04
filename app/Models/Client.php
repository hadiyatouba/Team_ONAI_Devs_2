<?php

namespace App\Models;

use App\Models\Scopes\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'user_id',
        'photo', // Ajout de la photo
    ];    

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new FilterScope());
    }

    public function scopeTelephone($query, $telephone)
    {
        return $query->where('telephone', $telephone);
    }

    public function scopeEtat($query, $etat)
    {
        return $query->where('etat', $etat);
    }
}
