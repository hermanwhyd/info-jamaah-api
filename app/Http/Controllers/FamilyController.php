<?php

namespace App\Http\Controllers;

use App\Http\Resources\FamilyResource;
use App\Repositories\FamilyRepository;

class FamilyController extends Controller
{

    protected $familyRepo;

    public function __construct(FamilyRepository $familyRepo)
    {
        $this->familyRepo = $familyRepo;
    }

    public function paging()
    {
        return $this->successRs(FamilyResource::collection($this->familyRepo->queryBuilder()->jsonPaginate()));
    }

    public function getAll()
    {
        return $this->successRs(FamilyResource::collection($this->familyRepo->queryBuilder()->get()));
    }

    public function findById($id)
    {
        return $this->successRs(new FamilyResource($this->familyRepo->queryBuilder()->whereId($id)->first()));
    }
}
