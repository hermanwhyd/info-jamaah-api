<?php
namespace App\Transformers;

use App\Models\Variable;

class VariableTransformer extends BaseTransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * Include resources without needing it to be requested.
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Turn this item object into a generic array.
     */
    public function transform(Variable $variable)
    {
        return [
            'id' => (int) $variable->id,
            'value' => $variable->value,
        ];
    }

}
