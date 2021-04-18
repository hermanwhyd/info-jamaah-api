<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jamaah extends Model
{
    use CamelCasing, SoftDeletes;

    const MEDIA_TAG_CLOSEUP = 'closeup';
    const MEDIA_TAG_THUMB = 'thumb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullName', 'nickName', 'birthDate', 'lvPembinaanEnum'
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
    ];

    /**
     * The attributes automatically turn into a Carbon object
     *
     * @var array
     */
    protected $dates = [
        'birthDate',
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];

    protected $with = [];

    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function family()
    {
        return $this->belongsToMany(Family::class, 'family_members')->whereStatus(Family::STATUS_ACTIVE)->latest();
    }

    public function families()
    {
        return $this->belongsToMany(Family::class, 'family_members')->latest();
    }

    public function details()
    {
        return $this->hasMany(JamaahDetail::class);
    }

    public function pembina()
    {
        return $this->belongsTo(Enum::class, 'pembina_enum', 'code')->where('group', 'like', 'PEMBINA_%');
    }

    public function lvPembinaan()
    {
        return $this->belongsTo(Enum::class, 'lv_pembinaan_enum', 'code')->where('group', 'LV_PEMBINAAN');
    }

    public function pembinaan()
    {
        return $this->hasOne(JamaahPembinaan::class)->active();
    }

    public function pembinaanHistories()
    {
        return $this->hasMany(JamaahPembinaan::class)->orderBy('id');
    }
}
