<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Enum extends Model
{
    use CamelCasing;

    protected $table = 'm_enums';

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
        'id', 'group', 'code', 'position', 'label',
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
        'position' => 'int',
        'removable' => 'boolean',
    ];

    public function variables()
    {
        return $this->morphMany(Variable::class, 'variable');
    }

    public function customFields()
    {
        return $this->hasMany(CustomField::class, 'group_enum_id', 'id')->orderBy('position');
    }

    public function enumables()
    {
        return $this->hasMany(Enumable::class, 'enum_id', 'id');
    }

    public function kepengurusans()
    {
        return $this->hasMany(Kepengurusan::class, 'dapukan_enum', 'code');
    }
}
