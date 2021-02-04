<?php

namespace App\Http\Controllers;

use App\Http\Resources\JamaahResource;
use App\Models\Jamaah;
use App\Repositories\JamaahRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'nickName' => 'required',
            'gender' => 'required',
            'pembinaEnum' => 'required|exists:m_enums,code',
            'photo' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $jamaah = new Jamaah($validator->validated());

        // Add media
        if ($request->filled('photo')) {
            $jamaah->addMediaFromRequest('photo')->toMediaCollection();
        }

        // Load missing relationship
        // $jamaah->loadMissing(['']);

        $data = new JamaahResource($jamaah);
        return $this->successRs($data);
    }

    public function updatePhoto(Request $request, $id)
    {

        // return $request;

        // $validator = Validator::make($request->all(), [
        //     'photo' => 'required|image'
        // ]);

        // if ($validator->fails()) {
        //     return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        // }

        $jamaah = Jamaah::findOrFail($id);

        // Replace media
        if ($request->filled('photo')) {
            $jamaah->addMediaFromUrl('https://pbs.twimg.com/profile_images/497717313072689153/PL0JEaGm_400x400.jpeg')->toMediaCollection();
        }

        return $this->successRs($jamaah->getFirstMediaUrl());
    }
}
