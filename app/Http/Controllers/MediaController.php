<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{

    public function downloadSingle($uuid)
    {
        return Media::findByUuid($uuid);
    }

    public function destroy($uuid)
    {
        $media = Media::findByUuid($uuid);
        $media->delete();
        return $this->successRs(null);
    }

    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'collectionName' => 'required|exists:m_enums,code',
            'properties.notes' => 'nullable|max:150',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $media = Media::findByUuid($uuid);
        $media->setCustomProperty('notes', $request->input('properties.notes'));
        $media->update(['name' => $request->name, 'collection_name' => $request->collectionName]);

        return $this->successRs(new MediaResource($media));
    }
}
