<?php

//Models/Scopes/FilterScope

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterScope implements Scope
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }
   
    public function apply(Builder $builder, Model $model)
    {
        if (isset($this->filters['etat'])) {
            $builder->where('etat', $this->filters['etat']);
        }

        if (isset($this->filters['telephone'])) {
            $builder->where('telephone', $this->filters['telephone']);
        }

        if (isset($this->filters['libelle'])) {
            $builder->where('libelle', 'like', "%{$this->filters['libelle']}%");
        }
    }
}
