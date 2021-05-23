<?php

namespace App\Repositories;

use App\Models\Location;
use Spatie\QueryBuilder\QueryBuilder;

class LocationRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Location::class)->allowedIncludes(['type']);
    }
}
