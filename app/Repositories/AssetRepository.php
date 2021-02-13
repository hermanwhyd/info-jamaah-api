<?php

namespace App\Repositories;

use App\Models\Asset;
use Spatie\QueryBuilder\QueryBuilder;

class AssetRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Asset::class)
            ->allowedFilters(['title'])
            ->allowedIncludes([
                'details.type', 'owner', 'category', 'status', 'location.type', 'audits.status', 'audits.location', 'audits.assetStatus',
                'maintenances.supplier', 'maintenances.type', 'maintenances.creator',
                'audits.assetStatus', 'audits.location.type'
            ]);
    }
}
