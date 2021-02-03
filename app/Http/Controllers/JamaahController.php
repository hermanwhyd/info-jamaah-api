<?php

namespace App\Http\Controllers;

use App\Http\Resources\JamaahResource;
use App\Repositories\JamaahRepository;

class JamaahController extends Controller
{

  protected $jamaahRepo;

  public function __construct(JamaahRepository $jamaahRepo)
  {
    $this->jamaahRepo = $jamaahRepo;
  }

  public function paging()
  {
    return JamaahResource::collection($this->jamaahRepo->queryBuilder()->jsonPaginate());
  }

  public function getAll()
  {
    $data = JamaahResource::collection($this->jamaahRepo->queryBuilder()->get());
    return $this->successRs($data);
  }

  public function findById($id)
  {
    $data = new JamaahResource($this->jamaahRepo->queryBuilder()->whereId($id)->first());
    return $this->successRs($data);
  }
}
