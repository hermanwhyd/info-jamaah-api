<?php
namespace App\Transformers;

use App\Models\Tag;

class TagTransformer extends BaseTransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['tagged'];

    /**
     * Include resources without needing it to be requested.
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Turn this item object into a generic array.
     */
    public function transform(Tag $tag)
    {
        return [
            'id' => (int) $tag->id,
            'group' => $tag->group,
            'tag' => $tag->tag,
            'slug' => $tag->slug,
            'bankDiklat' => $tag->tagged()->count(),
        ];
    }

    /**
     * User interested
     */
    public function includeInterested(Tag $model)
    {
        return $this->collectionOrNull($model->interested, new UserTransformer());
    }

}
