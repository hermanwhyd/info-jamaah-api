<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetAuditResource;
use App\Http\Resources\MediaResource;
use App\Models\Asset;
use App\Models\AssetAudit;
use App\Repositories\AssetAuditRepository;
use App\Utils\MediaUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetAuditController extends Controller
{

    private $assetAuditRepo;

    public function __construct(AssetAuditRepository $assetAuditRepo)
    {
        $this->assetAuditRepo = $assetAuditRepo;
    }

    public function paging()
    {
        return AssetAuditResource::collection($this->assetAuditRepo->queryBuilder()->jsonPaginate());
    }

    public function getAll()
    {
        $data = AssetAuditResource::collection($this->assetAuditRepo->queryBuilder()->get());
        return $this->successRs($data);
    }

    public function findById($id)
    {
        $data = new AssetAuditResource($this->assetAuditRepo->queryBuilder()->whereId($id)->first());
        return $this->successRs($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assetId' => 'required|numeric|exists:assets,id',
            'locationId' => 'required|numeric|exists:locations,id',
            'assetStatusEnum' => 'required|exists:m_enums,code',
            'notes' => 'nullable|max:150',
            'auditedAt' => 'date',
            'media' => 'nullable|exists:media,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $audit = AssetAudit::create($validator->validated());

        // Add media
        if ($request->filled('media')) {
            $audit->asset->addMedia('media');
        }

        $audit->loadMissing(['location', 'assetStatus']);

        $data = new AssetAuditResource($audit);
        return $this->successRs($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assetId' => 'required|numeric|exists:assets,id',
            'locationId' => 'required|numeric|exists:locations,id',
            'assetStatusEnum' => 'required|exists:m_enums,code',
            'notes' => 'nullable|max:150',
            'auditedAt' => 'date',
            'media' => 'nullable|exists:media,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $audit = AssetAudit::findOrFail($id);
        $audit->update($validator->validated());

        $data = new AssetAuditResource($audit);
        return $this->successRs($data);
    }

    public function destroy($id)
    {
        $deletedCount = AssetAudit::destroy($id);
        return $this->successRs($deletedCount);
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
            ->storingConversionsOnDisk('s3')
            ->toMediaCollection($collection);

        return new MediaResource($media);
    }
}
