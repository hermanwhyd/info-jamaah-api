<?php

namespace App\Http\Controllers;

use App\Foundations\Fractal\NoDataArraySerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class Controller extends BaseController
{

    private function getFractalManager()
    {
        $request = app(Request::class);
        $manager = new Manager();
        $manager->setSerializer(new NoDataArraySerializer());
        if (!empty($request->query('include'))) {
            $manager->parseIncludes($request->query('include'));
        }
        return $manager;
    }

    /**
     * Return single item
     */
    public function item($data, $transformer)
    {
        $manager = $this->getFractalManager();
        $resource = new Item($data, $transformer);
        return $manager->createData($resource)->toArray();
    }

    /**
     * Return collections
     */
    public function collection($data, $transformer)
    {
        $manager = $this->getFractalManager();
        $resource = new Collection($data, $transformer);
        return $manager->createData($resource)->toArray();
    }

    /**
     * Paginate json response
     */
    public function paginate($data, $transformer)
    {
        $manager = $this->getFractalManager();
        $resource = new Collection($data, $transformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($data));
        return $manager->createData($resource)->toArray();
    }

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
