<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class AssetAudit extends Model
{
    use CamelCasing;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'auditedAt', 'assetId', 'locationId', 'notes', 'nextAuditAt', 'assetStatusEnum'
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
        'nextAuditAt',
        'auditedAt'
    ];

    protected $with = [];

    public function assetStatus()
    {
        return $this->belongsTo(Enum::class, 'asset_status_enum', 'code')->where('group', 'ASSET_STATUS');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
