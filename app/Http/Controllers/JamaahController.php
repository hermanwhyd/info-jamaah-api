<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnumResource;
use App\Http\Resources\JamaahResource;
use App\Http\Resources\KepengurusanResource;
use App\Http\Resources\MediaResource;
use App\Models\Jamaah;
use App\Repositories\EnumRepository;
use App\Repositories\JamaahRepository;
use App\Repositories\KepengurusanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JamaahController extends Controller
{

    protected JamaahRepository $jamaahRepo;
    protected EnumRepository $enumRepo;
    protected KepengurusanRepository $kepengurusanRepo;

    public function __construct(JamaahRepository $jamaahRepo, EnumRepository $enumRepo, KepengurusanRepository $kepengurusanRepo)
    {
        $this->jamaahRepo = $jamaahRepo;
        $this->enumRepo = $enumRepo;
        $this->kepengurusanRepo = $kepengurusanRepo;
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

    public function findAddFieldsById(Request $request, $id)
    {
        $mode = $request->input('mode', 'edit');
        $query = $this->enumRepo->queryBuilder();

        if ($mode == 'view') {
            $query->whereHas('customFields.value', function ($query) use ($id) {
                $query->whereModelId($id);
            });
        }

        $query->with('customFields.value', function ($query) use ($id) {
            $query->whereModelId($id);
        })->whereGroup('CUSTOM_FIELD_JAMAAH')->orderBy('position');

        $data = EnumResource::collection($query->get(), $mode);
        return $this->successRs($data);
    }

    public function setAdditionalField(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customFieldId' => 'required|exists:custom_fields,id',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $jamaah = Jamaah::findOrFail($id);
        $result = $jamaah->additionalFields()->create($validator->validated());

        return $this->successRs($result);
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

        // $jamaah = new Jamaah($validator->validated());
        $jamaah = $this->jamaahRepo->queryBuilder()->create($validator->validated());

        // Add media
        if ($request->filled('photo')) {
            $jamaah->addMediaFromRequest('photo')->toMediaCollection();
        }

        // Load missing relationship
        // $jamaah->loadMissing(['']);

        $data = new JamaahResource($jamaah);
        return $this->successRs($data);
    }

    public function update(Request $request, $id)
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

        $jamaah = $this->jamaahRepo->queryBuilder()->whereId($id)->firstOrFail();
        $jamaah->update($validator->validated());
        $jamaah->save();

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
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'notes' => 'nullable|max:150'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $file = $request->file('file');
        $collection = Jamaah::MEDIA_TAG_PHOTO;
        $uniqid = uniqid();
        $fileName = $id . '_' . $collection . '_' . $uniqid . '.' . $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();

        $asset = Jamaah::findOrFail($id);
        $media = $asset->addMediaFromRequest('file')
            ->usingName($originalFileName)
            ->usingFileName($fileName)
            ->withCustomProperties(['notes' => $request->input('notes')])
            ->storingConversionsOnDisk('s3')
            ->toMediaCollection($collection);

        return new MediaResource($media);
    }

    public function getPembina($id)
    {
        $model = Jamaah::whereId($id)->with(['pembina'])->first();

        $data = [
            'KLP' => [
                'code' => $model->pembina->code,
                'label' => $model->pembina->label,
                'level' => 'Kelompok'
            ],
            'DS' => [
                'code' => 'JPS',
                'label' => 'Japos',
                'level' => 'Desa'
            ],
            'DA' => [
                'code' => 'TTM',
                'label' => 'Tangerang Timur',
                'level' => 'Daerah'
            ]
        ];

        return $this->successRs($data);
    }
}
