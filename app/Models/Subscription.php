<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Subscription extends Model
{
    use CamelCasing;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'
    ];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
    ];

    protected $with = [];

    public function subscribe()
    {
        return $this->morphTo();
    }

    public function subscriber()
    {
        return $this->morphTo();
    }
}
