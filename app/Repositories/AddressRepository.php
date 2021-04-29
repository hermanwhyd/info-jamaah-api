<?php

namespace App\Repositories;

use App\Models\Address;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AddressRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Address::class)
            ->allowedIncludes(['addressable'])
            ->allowedFilters(['kelurahan', 'kecamatan', AllowedFilter::trashed()]);
    }
}
