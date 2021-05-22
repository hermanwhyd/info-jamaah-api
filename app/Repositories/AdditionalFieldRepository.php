<?php

namespace App\Repositories;

use App\Models\CustomField;
use Spatie\QueryBuilder\QueryBuilder;

class AdditionalFieldRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(AdditionalField::class)->allowedIncludes(['customField']);
    }
}
