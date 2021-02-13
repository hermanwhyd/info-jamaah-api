<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class AssetDetail extends Model
{
    use CamelCasing;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assetId', 'typeEnum', 'value'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes cast to specific type
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'assetId' => 'int',
    ];

    /**
     * The attributes automatically turn into a Carbon object
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes eager load
     *
     * @var array
     */
    protected $with = [
        'type'
    ];

    public function type()
    {
        return $this->belongsTo(Enum::class, 'type_enum', 'code')->whereGroup('ASSET_DETAIL');
    }
}