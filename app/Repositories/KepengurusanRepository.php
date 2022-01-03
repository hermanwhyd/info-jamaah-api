<?php

namespace App\Repositories;

use App\Models\Kepengurusan;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class KepengurusanRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Kepengurusan::class)
            ->allowedIncludes(['pengurus', 'pembina', 'jamaah'])
            ->allowedFilters([AllowedFilter::trashed()]);
    }
}
