<?php

namespace App\Repositories;

use App\Models\Enum;
use Spatie\QueryBuilder\QueryBuilder;

class EnumRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Enum::class)->allowedIncludes(['enumables', '', 'customFields']);
    }
}
