<?php

namespace App\Http\Controllers;

use App\Http\Resources\JamaahResource;
use App\Models\Jamaah;
use App\Repositories\JamaahRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\MediaUploader;
use Intervention\Image\Facades\Image;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\Jobs\CreateImageVariants;

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

        $jamaah = Jamaah::withMediaAndVariants(Jamaah::MEDIA_TAG_CLOSEUP)->findOrFail($id);

        // Making photo intervention
        Image::make($file)
            ->resize(720, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save(public_path($folder) . $mainFileName);

        // Making mediable
        $newMedia = $mediaUploader->fromSource(public_path($folder . $mainFileName))->toDirectory('/profile')->upload();
        ImageManipulator::createImageVariant($newMedia, Jamaah::MEDIA_TAG_THUMB, true);

        // Delete old then add new one
        $oldMedia = $jamaah->firstMedia(Jamaah::MEDIA_TAG_CLOSEUP);
        if ($oldMedia) {
            foreach ($oldMedia->getAllVariants() as $key => $media) {
                $media->delete();
            }
            $oldMedia->delete();
        }

        $jamaah->attachMedia($newMedia, [Jamaah::MEDIA_TAG_CLOSEUP]);

        // Delete tmp file
        unlink(public_path($folder) . $mainFileName);

        return $this->successRs($jamaah->firstMedia([Jamaah::MEDIA_TAG_CLOSEUP]));
    }
}
