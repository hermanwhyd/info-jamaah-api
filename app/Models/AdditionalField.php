<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class AdditionalField extends Model
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
        'modelId', 'modelType', 'customFieldId', 'value'
    ];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'customFieldId' => 'int',
        'modelId' => 'int'
    ];

    /**
     * The attributes eager load
     *
     * @var array
     */
    protected $with = [];

    public function model()
    {
        return $this->morphTo();
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class)->orderBy('position');
    }
}
