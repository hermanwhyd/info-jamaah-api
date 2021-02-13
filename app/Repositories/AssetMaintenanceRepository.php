<?php

namespace App\Repositories;

use App\Models\AssetMaintenance;
use Spatie\QueryBuilder\QueryBuilder;

class AssetMaintenanceRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(AssetMaintenance::class)
            ->allowedFilters(['label'])
            ->allowedIncludes([
                'contacts', 'address', 'asset.details', 'asset.category', 'asset.owner', 'asset.location'
            ]);
    }
}
