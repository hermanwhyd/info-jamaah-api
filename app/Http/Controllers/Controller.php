<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
