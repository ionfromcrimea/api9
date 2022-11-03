<?php

namespace App\Http\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class MyFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
//        dd($property);
        $query->where('publication_year', $value[0]);
    }
}
