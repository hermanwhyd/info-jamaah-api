<?php

namespace App\Models;

use App\Traits\UserCreationLogger;
use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class AssetMaintenance extends Model
{
    use CamelCasing, UserCreationLogger;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'typeEnum', 'assetId', 'title', 'startDate', 'endDate', 'notes', 'supplierId'
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
    protected $dates = [
        'startDate',
        'endDate',
        'updatedAt',
        'deletedAt',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function type()
    {
        return $this->belongsTo(Enum::class, 'type_enum', 'code')->where('group', 'MAINTENANCE_TYPE');
    }
}
