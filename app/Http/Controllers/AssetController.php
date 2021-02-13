<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Repositories\AssetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\MediaUploader;
use Intervention\Image\Facades\Image;
use Plank\Mediable\Facades\ImageManipulator;

class AssetController extends Controller
{

    protected $assetRepo;

    public function __construct(AssetRepository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
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

    public function updatePhoto(Request $request, $id, MediaUploader $mediaUploader)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|image',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $file = $request->file('photo');
        $folder = 'media/temp/';
        $uniqid = uniqid();
        $mainFileName = $id . '_' . $uniqid . '.' . $file->getClientOriginalExtension();

        $asset = Asset::withMediaAndVariants(Asset::MEDIA_TAG_CLOSEUP)->findOrFail($id);

        // Making photo intervention
        Image::make($file)
            ->resize(720, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save(public_path($folder) . $mainFileName);

        // Making mediable
        $newMedia = $mediaUploader->fromSource(public_path($folder . $mainFileName))->toDirectory('/profile')->upload();
        ImageManipulator::createImageVariant($newMedia, Asset::MEDIA_TAG_THUMB, true);

        // Delete old then add new one
        $oldMedia = $asset->firstMedia(Asset::MEDIA_TAG_CLOSEUP);
        if ($oldMedia) {
            foreach ($oldMedia->getAllVariants() as $key => $media) {
                $media->delete();
            }
            $oldMedia->delete();
        }

        $asset->attachMedia($newMedia, [Asset::MEDIA_TAG_CLOSEUP]);

        // Delete tmp file
        unlink(public_path($folder) . $mainFileName);

        return $this->successRs($asset->firstMedia([Asset::MEDIA_TAG_CLOSEUP]));
    }
}
