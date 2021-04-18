<?php

namespace App\Repositories;

use App\Models\Jamaah;
use Spatie\QueryBuilder\QueryBuilder;

class JamaahRepository
{
    public function queryBuilder()
    {
        return QueryBuilder::for(Jamaah::class)
            ->allowedFilters(['full_name'])
            ->allowedIncludes([
                'details', 'pembina', 'pembinaan', 'lvPembinaan', 'pembinaanHistories', 'contacts',
                'family.members.relationship', 'family.kepalaKeluarga', 'family.members.jamaah', 'family.residance.type', 'family.residance.address',
                'families.members.relationship', 'families.kepalaKeluarga', 'families.members.jamaah', 'families.residance.type', 'families.residance.address'
            ]);
    }
}
