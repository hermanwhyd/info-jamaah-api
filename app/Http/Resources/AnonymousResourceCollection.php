<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use ReflectionClass;

class AnonymousResourceCollection extends ResourceCollection
{

    private $resourceRef;
    private $mode;

    public function __construct($resource, $clazz, $mode = null)
    {
        parent::__construct($resource);
        self::withoutWrapping();
        $this->resourceRef = new ReflectionClass($clazz);
        $this->mode = $mode;
    }

    public function toArray($request)
    {
        return $this->collection->map(function ($resource) use ($request) {
            return ($this->resourceRef->newInstanceArgs(array($resource, $this->mode)))->toArray($request);
        })->all();
    }
}
