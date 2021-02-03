<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Repositories\AddressRepository;

class AddressController extends Controller
{

  protected $addressRepo;

  public function __construct(AddressRepository $addressRepo)
  {
    $this->addressRepo = $addressRepo;
  }

  public function paging()
  {
    return AddressResource::collection($this->addressRepo->queryBuilder()->jsonPaginate());
  }

  public function getAll()
  {
    return AddressResource::collection($this->addressRepo->queryBuilder()->get());
  }

  public function findById($id)
  {
    return new AddressResource($this->addressRepo->queryBuilder()->whereId($id)->first());
  }
}
