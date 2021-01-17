<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    use CamelCasing;

    protected $table = 'taggables';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tagId', 'taggableId', 'taggableType',
    ];

    public function taggable()
    {
        return $this->morphTo();
    }

    public function tag()
    {
        return $this->hasMany(Tag::class, 'tag_id');
    }
}
