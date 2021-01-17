<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankDiklat;
use App\Models\SiapDiklat\Diklat;
use App\Models\SiapDiklat\Pegawai;
use App\Models\SiapDiklat\PegawaiAkun;
use App\Models\SiapDiklat\PendaftaranDiklat;
use App\Models\User;
use App\Transformers\BankDiklatTransformer;
use App\Transformers\UserTransformer;
use App\Utils\SecurityUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function paging(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        return $this->paginate(User::paginate($perPage), new UserTransformer());
    }

    public function findAll()
    {
        return $this->successRs($this->collection(User::with(['pegawai', 'roles'])->get(), new UserTransformer(UserTransformer::SIMPLIFY)));
    }

    public function find($id)
    {
        return $this->successRs($this->item(User::find($id), new UserTransformer()));
    }

    public function store(Request $request)
    {
        // Request Validation
        $validator = Validator::make($request->all(), [
            'nip' => 'required|numeric|digits:18|unique:users',
            'name' => 'required|between:2,100',
            'email' => 'required|email|unique:users|max:50',
            'mobile' => 'required|numeric|starts_with:08|digits_between:9,16|unique:users',
            'password' => 'required|string|min:6',
            'roles' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // Register as user
        $user = new User($validator->validated());
        $user->password = app('hash')->make($request->password);
        $user->save();
        $user->assignRole($request->roles);

        // Register as pegawai a.k.a akun if not exists
        if (!Pegawai::whereNip($request->nip)->exists()) {
            $pegawai = new PegawaiAkun($validator->validated());
            $pegawai->nama = $request->name;
            $pegawai->telpHp = $request->mobile;
            $pegawai->tipeAkunId = 1;
            $pegawai->registeredOn = date('Y-m-d H:i:s');
            $pegawai->statusAkunId = 2;
            $pegawai->activeOn = date('Y-m-d H:i:s');

            // Generate passwordd
            $salt = SecurityUtils::encrypt();
            $kataSandi = SecurityUtils::encrypt($request->password, $salt);
            $pegawai->salt = $salt;
            $pegawai->kataSandi = $kataSandi;

            $pegawai->save();
        }

        return $this->successRs($this->item($user, new UserTransformer(UserTransformer::SIMPLIFY)));
    }

    public function update(Request $request, $id)
    {
        // Request Validation
        $validator = Validator::make($request->all(), [
            'nip' => "required|numeric|digits:18|unique:users,nip,{$id}",
            'name' => 'required|between:2,100',
            'email' => "required|email|max:50|unique:users,email,{$id}",
            'mobile' => "required|numeric|starts_with:08|digits_between:9,16|unique:users,mobile,{$id}",
            'password' => 'sometimes|nullable|string|min:6',
            'roles' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // Register as user
        $user = User::with('pegawai')->find($id);
        $user->update($request->only(['nip', 'name', 'email', 'mobile']));
        $user->syncRoles($request->roles);

        // handle if password updated

        if ($request->filled('password') && !empty($request->password)) {
            $user->password = app('hash')->make($request->password);
            $user->save();
        }

        return $this->successRs($this->item($user, new UserTransformer(UserTransformer::SIMPLIFY)));
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return $this->successRs(null);
    }

    public function getInterest()
    {
        $model = auth()->user()->interests;
        return $this->successRs($this->collection($model, new BankDiklatTransformer()));
    }

    public function getDashboardEvaluation()
    {
        $user = auth()->user();
        $currentDiklat = PendaftaranDiklat::whereNip($user->nip)->where('statusDiklatId', '1')->first();
        if ($currentDiklat) {
            $evaluation = $user->evaluations()->where('diklat_id', $currentDiklat->diklatId)->first();
            $diklatQuestionnaires = Diklat::find($currentDiklat->diklatId)->questionnaires()->with('respondentType')->get();

            $result = [];
            foreach ($diklatQuestionnaires as $res) {
                $tmp['id'] = $res->questionnaire->id;
                $tmp['title'] = $res->questionnaire->title;
                $tmp['descriptioin'] = $res->questionnaire->description;
                $tmp['respondent'] = $res->respondentType->label;
                $tmp['completed'] = false;

                if ($evaluation) {
                    $questionnaire = $evaluation->questionnaires()->where('questionnaire_id', $res->questionnaireId)
                        ->where('completed_at', '!=', null)
                        ->first();
                    !$questionnaire ?: $tmp['completed'] = true;
                }

                $result[] = $tmp;
            }
            return $this->successRs($result);
        } else {
            return $this->successRs([]);
        }
    }

    public function storeInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bankDiklatId' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = auth()->user();
        try {
            $model->interests()->attach($request['bankDiklatId']);
        } catch (\Exception $e) {}

        return $this->successRs(null);
    }

    public function deleteInterest($id)
    {
        $model = auth()->user();
        $model->interests()->detach($id);
        return $this->successRs(null);
    }

    public function storeBankDiklat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'initiatorEnum' => 'required|exists:m_enums,code',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = User::find(2);
        $model->interests()->create($validator->validated());
        return $this->successRs(null);
    }

    public function getBankDiklatByCategory($id)
    {
        $bankDiklatIds = auth()->user()->interests()->pluck('bank_diklats.id')->toArray();
        $model = BankDiklat::whereHas('tags', function ($query) use ($id) {
            $query->where('id', $id);
        })
            ->whereNotIn('id', $bankDiklatIds)
            ->get();
        return $this->successRs($this->collection($model, new BankDiklatTransformer()));
    }

    public function getBankDiklatRecommended()
    {
        $user = auth()->user();
        $tagIds = $user->tags()->pluck('id')->toArray();
        $bankDiklatIds = $user->interests()->pluck('bank_diklats.id')->toArray();

        $model = BankDiklat::whereNotIn('id', $bankDiklatIds);
        if (count($tagIds) > 0) {
            $model->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('id', $tagIds);
            });
        }
        $model = $model->get();
        return $this->successRs($this->collection($model, new BankDiklatTransformer()));
    }

}
