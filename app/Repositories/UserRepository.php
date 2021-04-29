<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(User::class)
            ->allowedFields(['id', 'mobile', 'email', 'jamaah.id'])
            ->allowedIncludes(['jamaah'])
            ->allowedFilters(['email', 'mobile', 'jamaah.full_name', AllowedFilter::trashed()]);
    }
}
