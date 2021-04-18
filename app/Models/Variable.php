<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use CamelCasing;

    protected $table = 'variables';

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
        'group', 'value', 'variableId', 'variableName',
    ];

    public function variable()
    {
        return $this->morphTo();
    }
}
