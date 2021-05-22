<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdditionalFieldResource;
use App\Models\AdditionalField;
use App\Repositories\AdditionalFieldRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdditionalFieldController extends Controller
{

    protected AdditionalFieldRepository $adfRepo;

    public function __construct(AdditionalFieldRepository $adfRepo)
    {
        $this->adfRepo = $adfRepo;
    }

    public function findById($id)
    {
        return $this->successRs(new AdditionalFieldResource($this->jamaahRepo->queryBuilder()->whereId($id)->first()));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:additional_fields',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = AdditionalField::findOrFail($id);
        $model->update($validator->validated());

        return $this->successRs(new AdditionalFieldResource($model));
    }

    public function destroy($id)
    {
        $model = AdditionalField::findOrFail($id);
        $model->delete();

        return $this->successRs(null);
    }
}
