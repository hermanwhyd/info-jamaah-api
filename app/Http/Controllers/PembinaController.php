<?php

namespace App\Http\Controllers;

use App\Http\Resources\KepengurusanResource;
use App\Models\Enum;
use App\Models\Kepengurusan;
use App\Repositories\EnumRepository;
use App\Repositories\JamaahRepository;
use App\Repositories\KepengurusanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PembinaController extends Controller
{

    private $enumRepo;
    private $jamaahRepo;
    private $kepengurusanRepo;

    public function __construct(EnumRepository $enumRepo, JamaahRepository $jamaahRepo, KepengurusanRepository $kepengurusanRepo)
    {
        $this->enumRepo = $enumRepo;
        $this->jamaahRepo = $jamaahRepo;
        $this->kepengurusanRepo = $kepengurusanRepo;
    }

    public function getAll()
    {
        $lvList = Enum::whereGroup('LV_PEMBINA')->orderBy('position')->get();
        $pembinaList = Enum::where('group', 'like', 'PEMBINA_%')->orderBy('position')->get();

        $grouped = $pembinaList->mapToGroups(function ($item, $key) {
            return [
                Str::afterLast($item['group'], '_') => $item
            ];
        });

        $lvList->transform(function ($item, $key) use ($grouped) {
            return Arr::add($item, 'child', Arr::get($grouped, $item['code']));
        });

        return $lvList->all();
    }

    public function getOverview($initialPembina)
    {
        $pembina = $this->enumRepo->queryBuilder()->where('group', 'like', 'PEMBINA_%')->whereCode($initialPembina)->orderBy('position')->first();
        $jamaahCount = $this->jamaahRepo->queryBuilder()->where('pembina_enum', $initialPembina)->count();
        Arr::add($pembina, 'jamaahCount', $jamaahCount);

        return $this->successRs(Arr::only($pembina->toArray(), ['code', 'label', 'jamaahCount']));
    }

    public function getPengurusList($initialPembina)
    {
        return $this->successRs(KepengurusanResource::collection($this->kepengurusanRepo->queryBuilder()->wherePembinaEnum($initialPembina)->get()));
    }

    public function addPengurus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dapuanEnum' => 'required|exists:m_enums,code',
            'pembinaEnum' => 'required|exists:m_enums,code',
            'jamaahId' => 'required|exists:jamaahs,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // check if already exist
        $model = Kepengurusan::withTrashed()
            ->where('dapuan_enum', $request->dapuanEnum)
            ->where('pembina_enum', $request->pembinaEnum)
            ->where('jamaah_id', $request->jamaahId)
            ->first();

        if ($model) {
            $model->restore();
        } else {
            $model = Kepengurusan::create($validator->validated());
        }

        // Load required data
        $model->loadMissing(['jamaah', 'dapuan', 'pembina']);

        return $this->successRs(new KepengurusanResource($model));
    }

    public function removePengurus($initialPembina, $id)
    {
        Kepengurusan::findOrFail($id)->delete();
        return $this->successRs(null);
    }

    public function getCandidatPengurus(Request $request, $initialPembina)
    {
        $pembina = Enum::whereCode($initialPembina)->where('group', 'like', 'PEMBINA_%')->firstOrFail();

        $jamaahQ = $this->jamaahRepo->queryBuilder();

        // If pembina is kelompok
        if (Str::endsWith($pembina->group, '_KLP')) {
            $jamaahQ->wherePembinaEnum($pembina->code);
        }

        // if has lv pembinaan filter
        if ($request->filled('lv_pembinaan')) {
            $lvPembinaanList = Str::of($request->query('lv_pembinaan'))->explode(',');
            $jamaahQ->whereIn('lv_pembinaan_enum', $lvPembinaanList);
        }

        // If has pengurus, filter where not already in kepengurusan
        if ($request->filled('dapuan_enum')) {
            $dapuanEnum = $request->query('dapuan_enum');
            $jamaahQ->whereDoesntHave('dapuan', function ($query) use ($dapuanEnum) {
                $query->where('dapuan_enum', $dapuanEnum);
            });
        }

        $result = $jamaahQ->skip(0)->take(10)->get();

        return $this->successRs($result);
    }
}
