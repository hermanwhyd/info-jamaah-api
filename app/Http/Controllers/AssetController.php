<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Http\Resources\EnumResource;
use App\Http\Resources\MediaResource;
use App\Models\Asset;
use App\Models\Notifier;
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
        $mode = $request->input('mode', 'edit');
        $query = $this->enumRepo->queryBuilder();

        if ($mode == 'view') {
            $query->whereHas('customFields.value', function ($query) use ($id) {
                $query->whereModelId($id);
            });
        }

        $query->with('customFields.value', function ($query) use ($id) {
            $query->whereModelId($id);
        })->whereGroup('CUSTOM_FIELD_ASSET')->orderBy('position');

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

        $asset = Asset::findOrFail($id);
        $result = $asset->additionalFields()->create($validator->validated());

        return $this->successRs($result);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'tagNo' => 'nullable|max:15',
            'categoryEnum' => 'required|exists:m_enums,code',
            'statusEnum' => 'required|exists:m_enums,code',
            'pembinaEnum' => 'required|exists:m_enums,code',
            'locationId' => 'required|exists:locations,id',
            'photo' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $asset = new Asset($validator->validated());
        $asset = $this->assetRepo->queryBuilder()->create($validator->validated());

        // Add media
        if ($request->filled('photo')) {
            $asset->addMediaFromRequest('photo')->toMediaCollection();
        }

        $data = new AssetResource($asset);
        return $this->successRs($data);
    }

    public function copy(Request $request, $id)
    {
        $asset = $this->assetRepo->queryBuilder()->findOrFail($id);

        $newAsset = $asset->replicate();
        $newAsset->title = 'Copy of ' . $newAsset->title;
        $newAsset->tagNo = null;
        $newAsset->push();

        // Copy detail
        if ($request->boolean('detail')) {
            foreach ($asset->additionalFields as $item) {
                $newAsset->additionalFields()->create($item->toArray());
            }
        }

        // Copy maintenance
        if ($request->boolean('maintenance')) {
            foreach ($asset->maintenances as $item) {
                $newAsset->maintenances()->create($item->toArray());
            }
        }

        // Copy audit
        if ($request->boolean('audit')) {
            foreach ($asset->audits as $item) {
                $newAsset->audits()->create($item->toArray());
            }
        }

        // Copy notification
        if ($request->boolean('notification')) {
            foreach ($asset->notifiers as $item) {
                $newNotifier = $item->replicate();
                $newNotifier->push();
                $newNotifier->model()->associate($newAsset);
                $newNotifier->save();
            }
        }

        // Copy file
        if ($request->boolean('file')) {
            $asset->loadMissing('media');
            foreach ($asset->media as $item) {
                $item->copy($newAsset, $item->collection_name, $item->disk);
            }
        }

        //  Save all
        $newAsset->save();

        return $this->successRs($newAsset);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'tagNo' => 'nullable|max:15',
            'categoryEnum' => 'required|exists:m_enums,code',
            'statusEnum' => 'required|exists:m_enums,code',
            'pembinaEnum' => 'required|exists:m_enums,code',
            'locationId' => 'required|exists:locations,id',
            'photo' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $asset = $this->assetRepo->queryBuilder()->whereId($id)->firstOrFail();
        $asset->update($validator->validated());
        $asset->save();

        // Add media
        if ($request->filled('photo')) {
            $asset->addMediaFromRequest('photo')->toMediaCollection();
        }

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
            ->storingConversionsOnDisk('s3')
            ->toMediaCollection($collection);

        return new MediaResource($media);
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        foreach ($asset->additionalFields as $item) {
            $item->delete();
        }

        foreach ($asset->maintenances as $item) {
            $item->delete();
        }

        foreach ($asset->audits as $item) {
            $item->delete();
        }

        foreach ($asset->notifiers as $item) {
            $item->delete();
        }
        (new Notifier())->deleteMorphResidual();

        foreach ($asset->media as $item) {
            $item->delete();
        }

        $deleted = $asset->delete();

        return $this->successRs($deleted);
    }
}
