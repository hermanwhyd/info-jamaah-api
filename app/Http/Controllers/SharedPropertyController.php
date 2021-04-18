<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnumTypeResource;
use App\Models\Enum;
use App\Repositories\EnumRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SharedPropertyController extends Controller
{

    protected EnumRepository $enumRepo;

    public function __construct(EnumRepository $enumRepo)
    {
        $this->enumRepo = $enumRepo;
    }

    /**
     * Get all shared property within group
     */
    public function getByGroup($group)
    {
        return $this->successRs(EnumTypeResource::collection($this->enumRepo->queryBuilder()->whereGroup($group)->get()));
    }

    /**
     * Batch update shared property with payload list of enum (id, label)
     */
    public function batchUpdate(Request $request)
    {
        $data = ['data' => $request->all()];
        $validator = Validator::make($data, [
            'data.*.id' => 'required|numeric|exists:m_enums',
            'data.*.label' => 'required|max:400',
            'data.*.position' => 'sometimes|numeric',
        ]);
        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // Loop for update
        foreach ($request->all() as $enum) {
            Enum::find($enum['id'])->update($enum);
        }

        return $this->successRs(null);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'group' => 'required',
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = Enum::create($validator->validated());

        return $this->successRs(new EnumTypeResource($model));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'group' => 'required',
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = Enum::findOrFail($id);
        $model->update($validator->validated());

        return $this->successRs(new EnumTypeResource($model));
    }

    public function destroy($id)
    {
        $model = Enum::findOrFail($id);
        if (!$model->removable) {
            return $model;
            throw new Exception("Property enum with id: {$id} is not removable!");
        }

        $model->images()->delete();
        $model->variables()->delete();
        $model->delete();

        return $this->successRs(null);
    }
}
