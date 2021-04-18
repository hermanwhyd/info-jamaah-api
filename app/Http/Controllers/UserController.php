<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function paging()
    {
        return UserResource::collection(
            $this->userRepo->queryBuilder()->jsonPaginate()
        );
    }

    public function findAll()
    {
        return $this->successRs(
            UserResource::collection(
                $this->userRepo->queryBuilder()->get()
            )
        );
    }

    public function find($id)
    {
        return $this->successRs(
            new UserResource($this->userRepo->queryBuilder()->where('id', $id)->first())
        );
    }

    public function store(Request $request)
    {
        // Request Validation
        $validator = Validator::make($request->all(), [
            'jamaahId' => 'required|exists:jamaahs,id',
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

        return $this->successRs(new UserResource($user->loadMissing('jamaah')));
    }

    public function update(Request $request, $id)
    {
        // Request Validation
        $validator = Validator::make($request->all(), [
            'email' => "required|email|max:50|unique:users,email,{$id}",
            'mobile' => "required|numeric|starts_with:08|digits_between:9,16|unique:users,mobile,{$id}",
            'password' => 'sometimes|nullable|string|min:6',
            'roles' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
        }

        // Register as user
        $user = User::find($id);
        $user->update($validator->validated());
        $user->syncRoles($request->roles);

        // handle if password updated

        if ($request->filled('password') && !empty($request->password)) {
            $user->password = app('hash')->make($request->password);
            $user->save();
        }

        return $this->successRs(new UserResource($user->loadMissing('jamaah')));
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return $this->successRs(null);
    }
}
