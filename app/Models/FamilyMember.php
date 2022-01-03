<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class FamilyMember extends Model
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
        'id', 'familyId', 'relationshipEnum', 'status', 'position'
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
        'familyId' => 'int',
        'position' => 'int',
    ];

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Enum::class, 'relationship_enum', 'code')->whereGroup('FAMS_RELATIONSHIP');
    }
}
