<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SiapDiklat\Diklat;
use App\Models\Variable;
use App\Transformers\VariableTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{

    /**
     * Get all evaluation data feed
     */
    public function evaluationDataFeed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $year = $request->year;
        $models = Variable::with(['variable' => function ($q) use ($year) {
            $q->where('tahunDiklat', '=', $year);
        }])->whereGroup(Diklat::EV_DASHBOARD)->orderBy('id', 'desc')->get();

        // Only for not null diklat
        $filtered = [];
        foreach ($models as $model) {
            if ($model->variable != null) {
                $filtered[] = $model;
            }
        }
        return $this->successRs($this->collection($filtered, new VariableTransformer()));
    }

}
