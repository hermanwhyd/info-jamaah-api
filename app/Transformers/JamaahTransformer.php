<?php
namespace App\Transformers;

use App\Models\User;
use App\Utils\DateUtils;

class JamaahTransformer extends BaseTransformerAbstract
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
    public function transform(User $model)
    {
        return [
            'id' => (int) $model->id,
            'fullName' => $model->full_name,
            'prefixName' => $model->prefix_name,
            'suffixName' => $model->suffix_name,
            'createdAt' => DateUtils::toIso8601String($model->created_at),
            'updatedAt' => DateUtils::toIso8601String($model->updated_at),
        ];
    }

}
