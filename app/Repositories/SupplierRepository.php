<?php

namespace App\Repositories;

use App\Models\Supplier;
use Spatie\QueryBuilder\QueryBuilder;

class SupplierRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Supplier::class)->allowedIncludes(['address', 'contacts', 'maintenances']);
    }
}
