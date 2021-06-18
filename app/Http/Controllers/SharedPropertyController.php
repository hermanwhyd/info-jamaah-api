<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdditionalFieldResource;
use App\Http\Resources\EnumResource;
use App\Http\Resources\EnumTypeResource;
use App\Http\Resources\LocationResource;
use App\Models\AdditionalField;
use App\Models\Asset;
use App\Models\Enum;
use App\Repositories\EnumRepository;
use App\Repositories\LocationRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SharedPropertyController extends Controller
{

    protected EnumRepository $enumRepo;
    protected LocationRepository $locationRepo;

    public function __construct(EnumRepository $enumRepo, LocationRepository $locationRepo)
    {
        $this->enumRepo = $enumRepo;
        $this->locationRepo = $locationRepo;
    }

    /**
     * Get all shared property within group
     */
    public function getByGroup(Request $request, $group)
    {
        if ($request->query('mode', 'view') == 'edit')
            return $this->successRs(EnumResource::collection($this->enumRepo->queryBuilder()->whereGroup($group)->orderBy('position')->get()));

        return $this->successRs(EnumTypeResource::collection($this->enumRepo->queryBuilder()->whereGroup($group)->orderBy('position')->get()));
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

        if ($model) {
            $model = Enum::find($model->id);
        }

        return $this->successRs(new EnumResource($model));
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

        return $this->successRs(new EnumResource($model));
    }

    public function destroy($id)
    {
        $model = Enum::findOrFail($id);
        if (!$model->removable) {
            return $model;
            throw new Exception("Property enum with id: {$id} is not removable!");
        }

        $model->variables()->delete();
        $model->customFields()->delete();
        $model->delete();

        return $this->successRs(null);
    }

    public function getOptionBySelector(Request $request, $selector)
    {
        if ($selector === 'location') {
            $data = $this->locationRepo->queryBuilder()->get();
            return $this->successRs(LocationResource::collection($data));
        }

        if ($selector === 'pembina') {
            $data = $this->enumRepo->queryBuilder()->where('group', 'like', 'PEMBINA_%')->orderBy('position')->get();
            return $this->successRs(EnumTypeResource::collection($data));
        }

        if ($selector === 'asset-af') {
            $assetId = $request->query('id');
            $addFields = AdditionalField::with(
                'customField'
            )->whereHas(
                'customField',
                function (Builder $query) {
                    $query->where('field_type', 'date');
                }
            )->whereHasMorph(
                'model',
                [Asset::class],
                function (Builder $query, $type) use ($assetId) {
                    $query->where('model_id', $assetId);
                }
            )->get();

            return $this->successRs(AdditionalFieldResource::collection($addFields));
        }

        if ($selector === 'pengrs-wc') {
            $data = Enum::whereGroup('PENGURUS')->withCount('enumables')->get();
            return $this->successRs(EnumResource::collection($data));
        }

        return $this->errorRs('failed', `Tidak ada selector ${selector}`, null, 400);
    }
}
