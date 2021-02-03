<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

  /**
   * Return success json response with http code 200
   */
  public function successRs($data, $message = null)
  {
    return response()->json([
      "status" => "ok",
      "message" => $message ?: "Permintaan berhasil diproses",
      "data" => (is_array($data) && Arr::exists($data, 'data')) ? $data['data'] : $data,
    ], 200);
  }

  /**
   * Return error json response with parameterize response
   */
  public function errorRs($status, $message, $data, int $httpStatusCode)
  {
    return response()->json([
      "status" => $status,
      "message" => $message,
      "data" => $data,
    ], $httpStatusCode);
  }
}
