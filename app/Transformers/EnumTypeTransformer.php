<?php
namespace App\Transformers;

use App\Models\Enum as ModelsEnum;
use Illuminate\Support\Arr;

class EnumTypeTransformer extends BaseTransformerAbstract
{
    public const FULL = "full";

    protected $template;

    public function __construct($template = 'default')
    {
        $this->template = $template;
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['variables'];

    /**
     * Turn this item object into a generic array.
     *
     * @param  \App\Enum  $enum
     * @return array
     */
    public function transform(ModelsEnum $enum)
    {
        $result = [
            'id' => $enum->id,
            'code' => $enum->code,
            'label' => $enum->label,
            'position' => $enum->position,
            'removable' => $enum->removable,
        ];

        if ($this->template == self::FULL) {
            return $result;
        } else {
            return Arr::only($result, ['id', 'code', 'label']);
        }
    }

    public function includeVariables(ModelsEnum $model)
    {
        return $this->collectionOrNull($model->variables, new VariableTransformer());
    }

}
