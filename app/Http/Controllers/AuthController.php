<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SiapDiklat\Pegawai;
use App\Models\SiapDiklat\PegawaiAkun;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Utils\SecurityUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

  public function register(Request $request)
  {

    // Request Validation
    $validator = Validator::make($request->all(), [
      'nip' => 'required|numeric|digits:18|unique:users',
      'name' => 'required|between:2,100',
      'email' => 'required|email|unique:users|max:50',
      'mobile' => 'required|numeric|starts_with:08|digits_between:9,16|unique:users',
      'password' => 'required|confirmed|string|min:6',
    ]);

    if ($validator->fails()) {
      return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
    }

    // Register as user
    $user = new User($validator->validated());
    $user->password = app('hash')->make($request->password);
    $user->save();
    $user->assignRole('PEGAWAI');

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

    return $this->successRs($this->item($user, new UserTransformer()));
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {

    // Request Validation
    $validator = Validator::make($request->all(), [
      'email' => 'sometimes|required|email',
      'mobile' => 'sometimes|required|numeric',
      'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return $this->errorRs("failed", "Data yang dikirim tidak valid", $validator->errors()->all(), 400);
    }

    // Authentication
    // $credentials = request(['nip', 'password']);

    if (!$token = auth()->attempt($validator->validated())) {
      return $this->errorRs("failed", "NIP dan Password tidak sesuai", null, 401);
    }

    return $this->respondWithToken($token);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return $this->successRs($this->item(auth()->user(), new UserTransformer()));
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth()->logout();

    return $this->successRs(null, 'Successfully logged out');
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return $this->successRs(
      [
        'token' => $token,
        'type' => 'bearer',
        'expiresIn' => auth()->factory()->getTTL() * 60,
      ]
    );
  }
}
