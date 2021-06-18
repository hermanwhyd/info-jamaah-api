<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Enumable extends Model
{
    use CamelCasing;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'enum_id',
    ];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
    ];

    protected $with = ['model'];

    public function enum()
    {
        return $this->belongsTo(Enum::class, 'enum_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
