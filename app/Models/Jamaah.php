<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Jamaah extends Model implements HasMedia
{
    use CamelCasing, SoftDeletes, InteractsWithMedia;

    const MEDIA_TAG_PHOTO = 'PHOTO';
    const MEDIA_TAG_THUMB = 'THUMB';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullName', 'nickname', 'birthDate', 'lvPembinaanEnum'
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

    public function kepengurusans()
    {
        return $this->hasMany(Kepengurusan::class);
    }

    public function avatar()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')->latest();
    }

    public function photos()
    {
        return $this->media()->whereCollectionName(self::MEDIA_TAG_PHOTO)->latest();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_TAG_PHOTO)
            ->useDisk('s3')
            ->onlyKeepLatest(100);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(self::MEDIA_TAG_THUMB)
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    public function additionalFields()
    {
        return $this->morphMany(AdditionalField::class, 'model');
    }
}
