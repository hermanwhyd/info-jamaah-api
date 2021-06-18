<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotifierResource;
use App\Models\AdditionalField;
use App\Models\Asset;
use App\Models\Enum;
use App\Models\Notifier;
use App\Models\Subscription;
use App\Repositories\NotifierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotifierController extends Controller
{

    protected NotifierRepository $notifierRepo;

    public function __construct(NotifierRepository $notifierRepo)
    {
        $this->notifierRepo = $notifierRepo;
    }

    public function getAll()
    {
        $data = NotifierResource::collection($this->notifierRepo->queryBuilder()->get());
        return $this->successRs($data);
    }

    public function findById($id)
    {
        $data = new NotifierResource($this->notifierRepo->queryBuilder()->whereId($id)->first());
        return $this->successRs($data);
    }


    public function storeForAsset(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'isRepetition' => 'required|boolean',
            'reminderDays' => 'required',
            'dueDateAt' => 'nullable|date',
            'referable.id' => 'nullable|exists:additional_fields,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $notifier = $this->notifierRepo->queryBuilder()->create($validator->validated());
        $notifier->model()->associate(Asset::find($id));
        if ($request->has('referable.id')) {
            $notifier->referable()->associate(AdditionalField::find($request->input('referable.id')));
        }
        $notifier->save();

        $data = new NotifierResource($notifier);
        return $this->successRs($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'isRepetition' => 'required|boolean',
            'reminderDays' => 'required',
            'dueDateAt' => 'nullable|date',
            'referable.id' => 'nullable|exists:additional_fields,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $notifier = $this->notifierRepo->queryBuilder()->findOrFail($id);
        $notifier->update($validator->validated());
        if ($request->has('referable.id')) {
            $notifier->referable()->associate(AdditionalField::find($request->input('referable.id')));
        } else {
            $notifier->referable()->dissociate();
        }
        $notifier->save();

        $data = new NotifierResource($notifier);
        return $this->successRs($data);
    }

    public function destroy($id)
    {
        $deleted = Notifier::findOrFail($id);
        return $this->successRs($deleted);
    }

    public function subscribe(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subscriber.id' => 'nullable|exists:m_enums,id'
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        $model = Notifier::findOrFail($id);
        $subs = new Subscription();
        $subs->subscriber()->associate(Enum::findOrFail($request->input('subscriber.id')));
        $model->subscriptions()->save($subs);
        $model->save();

        return $this->successRs($subs);
    }

    public function unsubscribe($id, $subId)
    {
        $deletedCount = Subscription::destroy($subId);
        return $this->successRs($deletedCount);
    }
}
