<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
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
        'groupEnumId', 'fieldName', 'fieldType', 'fieldReference', 'position'
    ];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'groupEnumId' => 'int',
        'position' => 'int'
    ];

    /**
     * The attributes eager load
     *
     * @var array
     */
    protected $with = [];

    public function group()
    {
        return $this->belongsTo(Enum::class, 'group_enum_id', 'id');
    }

    public function additionalFields()
    {
        return $this->hasMany(AdditionalField::class);
    }

    public function value()
    {
        return $this->hasOne(AdditionalField::class);
    }
}
