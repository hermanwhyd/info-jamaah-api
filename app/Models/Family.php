<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Family extends Model
{
    use CamelCasing;

    const STATUS_ACTIVE = 'A';
    const STATUS_PISAH_KK = 'P';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'kepalaKeluargaId', 'pembinaEnum', 'residanceId', 'label'
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
        'kepalaKeluargaId' => 'int',
        'residanceId' => 'int',
    ];

    public function kepalaKeluarga()
    {
        return $this->belongsTo(Jamaah::class, 'kepalaKeluargaId');
    }

    public function residance()
    {
        return $this->belongsTo(Residance::class);
    }

    public function members()
    {
        return $this->hasMany(FamilyMember::class)->orderBy('position');
    }

    public function pembina()
    {
        return $this->belongsTo(Enum::class, 'pembina_enum', 'code')->where('group', 'like', 'PEMBINA_%')->orderBy('position');
    }

}
