<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetMaintenanceResource;
use App\Http\Resources\MediaResource;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Repositories\AssetMaintenanceRepository;
use App\Utils\MediaUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetMaintenanceController extends Controller
{

    protected $assetMntnRepo;

    public function __construct(AssetMaintenanceRepository $assetMntnRepo)
    {
        $this->assetMntnRepo = $assetMntnRepo;
    }

    public function paging()
    {
        return AssetMaintenanceResource::collection($this->assetMntnRepo->queryBuilder()->jsonPaginate());
    }

    public function getAll()
    {
        $data = AssetMaintenanceResource::collection($this->assetMntnRepo->queryBuilder()->get());
        return $this->successRs($data);
    }

    public function findById($id)
    {
        $data = new AssetMaintenanceResource($this->assetMntnRepo->queryBuilder()->whereId($id)->first());
        return $this->successRs($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assetId' => 'required|numeric|exists:assets,id',
            'supplierId' => 'required|exists:suppliers,id',
            'typeEnum' => 'required|exists:m_enums,code',
            'title' => 'required',
            'notes' => 'nullable|max:150',
            'startDate' => 'date',
            'endDate' => 'date',
            'media' => 'nullable|exists:media,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $maintenance = AssetMaintenance::create($validator->validated());

        // Add media
        if ($request->filled('media')) {
            $maintenance->asset->addMedia('media');
        }

        $data = new AssetMaintenanceResource($maintenance);
        return $this->successRs($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assetId' => 'required|numeric|exists:assets,id',
            'supplierId' => 'required|exists:suppliers,id',
            'typeEnum' => 'required|exists:m_enums,code',
            'title' => 'required',
            'notes' => 'nullable|max:150',
            'startDate' => 'date',
            'endDate' => 'date',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $maintenance = AssetMaintenance::findOrFail($id);
        $maintenance->update($validator->validated());

        $data = new AssetMaintenanceResource($maintenance);
        return $this->successRs($data);
    }

    public function destroy($id)
    {
        $deletedCount = AssetMaintenance::destroy($id);
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
            ->storingConversionsOnDisk('media')
            ->toMediaCollection($collection);

        return new MediaResource($media);
    }
}
