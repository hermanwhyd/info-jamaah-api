<?php

namespace App\Models;

use App\Image;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use CamelCasing;

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
        'group', 'tag', 'slug',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [];

    public function related()
    {
        return $this->hasMany(Taggable::class);
    }

    public function tagged()
    {
        return $this->morphedByMany(BankDiklat::class, 'taggable');
    }

    public function interested()
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

}
