<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomFieldResource;
use App\Models\CustomField;
use App\Repositories\CustomFieldRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomFieldController extends Controller
{

    protected CustomFieldRepository $customFieldRepo;

    public function __construct(CustomFieldRepository $customFieldRepo)
    {
        $this->customFieldRepo = $customFieldRepo;
    }

    public function getByGroup($groupId)
    {
        return $this->successRs(CustomFieldResource::collection($this->enumRepo->queryBuilder()->whereGroupEnumId($groupId)->orderBy('position')->get()));
    }

    public function batchUpdate(Request $request)
    {
        $data = ['data' => $request->all()];
        $validator = Validator::make($data, [
            'data.*.id' => 'required|numeric|exists:custom_fields',
            'data.*.position' => 'sometimes|numeric',
        ]);
        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // Loop for update
        foreach ($request->all() as $enum) {
            CustomField::find($enum['id'])->update($enum);
        }

        return $this->successRs(null);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fieldName' => 'required',
            'fieldType' => 'required',
            'groupEnumId' => 'required|exists:m_enums,id',
            'fieldReference' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = CustomField::create($validator->validated());
        if ($model) {
            $model = CustomField::find($model->id);
        }

        return $this->successRs(new CustomFieldResource($model));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fieldName' => 'required',
            'fieldType' => 'required',
            'fieldReference' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = CustomField::findOrFail($id);
        $model->update($validator->validated());

        return $this->successRs(new CustomFieldResource($model));
    }

    public function destroy($id)
    {
        $model = CustomField::findOrFail($id);
        $model->delete();

        return $this->successRs(null);
    }
}
