<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Asset extends Model
{
    use CamelCasing, SoftDeletes, Mediable;

    const MEDIA_TAG_CLOSEUP = 'closeup';
    const MEDIA_TAG_THUMB = 'thumb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'tagNo', 'categoryEnum', 'location', 'statusEnum', 'ownerEnum'
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
        'createdById' => 'int',
        'locationId' => 'int',
    ];

    /**
     * The attributes automatically turn into a Carbon object
     *
     * @var array
     */
    protected $dates = [
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];

    protected $with = [];

    public function details()
    {
        return $this->hasMany(AssetDetail::class);
    }

    public function owner()
    {
        return $this->belongsTo(Enum::class, 'owner_enum', 'code')->where('group', 'like', 'PEMBINA_%');
    }

    public function category()
    {
        return $this->belongsTo(Enum::class, 'category_enum', 'code')->where('group', 'ASSET_CATEGORY');
    }

    public function status()
    {
        return $this->belongsTo(Enum::class, 'status_enum', 'code')->where('group', 'ASSET_STATUS');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function audits()
    {
        return $this->hasMany(AssetAudit::class);
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
