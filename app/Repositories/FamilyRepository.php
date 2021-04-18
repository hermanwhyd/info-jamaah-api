<?php

namespace App\Repositories;

use App\Models\Family;
use Spatie\QueryBuilder\QueryBuilder;

class FamilyRepository
{
  public function queryBuilder()
  {
    return QueryBuilder::for(Family::class)
      ->allowedIncludes(['familyMembers', 'kepalaKeluarga', 'residance']);
  }
}
