<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Address extends Model
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
        'id', 'addressable_type', 'addressable_id', 'streetName', 'houseNo', 'rt', 'rw', 'kelurahan', 'kecamatan', 'city', 'postCode', 'geo'
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
        'addressable_id' => 'int',
        'postCode' => 'int',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
