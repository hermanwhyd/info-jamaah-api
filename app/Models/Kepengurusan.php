<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kepengurusan extends Model
{
    use CamelCasing, SoftDeletes;

    protected $table = 'kepengurusan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'pengurusEnum', 'pembinaEnum', 'jamaahId', 'description'
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
        'jamaahId' => 'int',
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

    public function pembina()
    {
        return $this->belongsTo(Enum::class, 'pembina_enum', 'code')->where('group', 'like', 'PEMBINA_%');
    }

    public function pengurus()
    {
        return $this->belongsTo(Enum::class, 'pengurus_enum', 'code')->where('group', 'like', 'PENGURUS_%');
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }
}
