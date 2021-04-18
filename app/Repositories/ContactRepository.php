<?php

namespace App\Repositories;

use App\Models\Contact;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ContactRepository
{
  public function queryBuilder()
  {
    return QueryBuilder::for(Contact::class)
      ->allowedIncludes(['jamaah'])
      ->allowedFilters(['value', 'contactType', AllowedFilter::trashed()]);
  }
}
