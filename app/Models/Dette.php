<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dette extends Model
{
    use HasFactory;
    
    protected $fillable = ['date', 'montant', 'montantDu', 'montantRestant', 'client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withPivot('qteVente', 'prixVente');
    }
}
