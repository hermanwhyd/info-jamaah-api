<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

abstract class BaseTransformerAbstract extends TransformerAbstract {

    public function collectionOrNull($data, $transformer) {
        return ($data) ? $this->collection($data, $transformer, 'include') : null;
    }

    public function itemOrNull($data, $transformer) {
        return ($data) ? $this->item($data, $transformer, 'include') : $this->primitive(null);
    }

}
