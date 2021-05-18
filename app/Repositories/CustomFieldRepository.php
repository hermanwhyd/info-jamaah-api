<?php

namespace App\Repositories;

use App\Models\CustomField;
use Spatie\QueryBuilder\QueryBuilder;

class CustomFieldRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(CustomField::class)->allowedIncludes(['group', 'additionalFields']);
    }
}
