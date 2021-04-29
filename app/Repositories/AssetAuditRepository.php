<?php

namespace App\Repositories;

use App\Models\AssetAudit;
use Spatie\QueryBuilder\QueryBuilder;

class AssetAuditRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(AssetAudit::class)
            ->allowedIncludes([
                'creator', 'assetStatus', 'location', 'asset'
            ]);
    }
}
