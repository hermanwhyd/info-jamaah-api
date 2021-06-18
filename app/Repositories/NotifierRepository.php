<?php

namespace App\Repositories;

use App\Models\Notifier;
use Spatie\QueryBuilder\QueryBuilder;

class NotifierRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Notifier::class)
            ->allowedIncludes([
                'model', 'referable'
            ]);
    }
}
