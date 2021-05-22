<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Http\Resources\EnumResource;
use App\Http\Resources\MediaResource;
use App\Models\Asset;
use App\Repositories\AssetRepository;
use App\Repositories\EnumRepository;
use App\Utils\MediaUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{

    protected AssetRepository $assetRepo;
    protected EnumRepository $enumRepo;

    public function __construct(AssetRepository $assetRepo, EnumRepository $enumRepo)
    {
        $this->assetRepo = $assetRepo;
        $this->enumRepo = $enumRepo;
    }

    public function paging()
    {
        return AssetResource::collection($this->assetRepo->queryBuilder()->jsonPaginate());
    }

    public function getAll()
    {
        $data = AssetResource::collection($this->assetRepo->queryBuilder()->get());
        return $this->successRs($data);
    }

    public function findById($id)
    {
        $data = new AssetResource($this->assetRepo->queryBuilder()->whereId($id)->first());
        return $this->successRs($data);
    }

    public function findAddFieldsById(Request $request, $id)
    {
        $groups = $this->enumRepo->queryBuilder()->with(['customFields.value' => function ($query) use ($id) {
            $query->whereModelId($id);
        }])->whereGroup('CUSTOM_FIELD_ASSET')->orderBy('position')->get();

        $data = EnumResource::collection($groups);
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

        $asset = Asset::findOrFail($id);
        $result = $asset->additionalFields()->create($validator->validated());

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

        $asset = new Asset($validator->validated());

        // Add media
        if ($request->filled('photo')) {
            $asset->addMediaFromRequest('photo')->toMediaCollection();
        }

        // Load missing relationship
        // $asset->loadMissing(['']);

        $data = new AssetResource($asset);
        return $this->successRs($data);
    }

    public function upload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
            'notes' => 'nullable|max:150'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $file = $request->file('file');
        $collection = MediaUtils::isImage($file->getClientMimeType()) ? Asset::MEDIA_TAG_PHOTO : Asset::MEDIA_TAG_DOCS;
        $uniqid = uniqid();
        $fileName = $id . '_' . $collection . '_' . $uniqid . '.' . $file->getClientOriginalExtension();
        $originalFileName = $file->getClientOriginalName();

        $asset = Asset::findOrFail($id);
        $media = $asset->addMediaFromRequest('file')
            ->usingName($originalFileName)
            ->usingFileName($fileName)
            ->withCustomProperties(['notes' => $request->input('notes')])
            ->storingConversionsOnDisk('media')
            ->toMediaCollection($collection);

        return new MediaResource($media);
    }
}
