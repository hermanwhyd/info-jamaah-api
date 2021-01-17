<?php
namespace App\Transformers;

use App\Models\User;
use App\Utils\Common;
use App\Utils\DateUtils;
use Illuminate\Support\Arr;

class UserTransformer extends BaseTransformerAbstract
{

    public const SIMPLIFY = "simplify";

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
    protected $availableIncludes = ['tags', 'jamaah'];

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
        $result = [
            'id' => (int) $model->id,
            'nip' => $model->nip,
            'name' => $model->name,
            'mobile' => $model->mobile,
            'email' => $model->email,
            'createdAt' => DateUtils::toIso8601String($model->created_at),
            'updatedAt' => DateUtils::toIso8601String($model->updated_at),
        ];

        if ($this->template == self::SIMPLIFY) {
            $filtered = Arr::only($result, ['id', 'nip', 'name', 'mobile', 'email']);
            return array_merge($filtered, [
                'roles' => $model->roles->pluck('name'),
                'pegawai' => [
                    'photo' => Common::photoUrl($model->pegawai->photo ?? 'default-picture.png'),
                ],
            ]);
        } else {
            return $result;
        }
    }

    public function includeTags(User $model)
    {
        return $this->collectionOrNull($model->tags, new TagTransformer());
    }

    public function includeJamaah(User $model)
    {
        return $this->itemOrNull($model->jamaah, new JamaahTransformer);
    }
}
