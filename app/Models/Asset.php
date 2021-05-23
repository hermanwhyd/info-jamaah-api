<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Asset extends Model implements HasMedia
{
    use CamelCasing, SoftDeletes, InteractsWithMedia;

    const MEDIA_TAG_THUMB = 'THUMB';
    const MEDIA_TAG_PHOTO = 'PHOTO';
    const MEDIA_TAG_DOCS = 'DOCS';

    const CUSTOMFIELD_GROUP = 'CUSTOM_FIELD_ASSET';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'tagNo', 'categoryEnum', 'location', 'statusEnum', 'pembinaEnum'
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

    public function pembina()
    {
        return $this->belongsTo(Enum::class, 'pembina_enum', 'code')->where('group', 'like', 'PEMBINA_%');
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

    public function avatar()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')->latest();
    }

    public function additionalFields()
    {
        return $this->morphMany(AdditionalField::class, 'model');
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

        $this
            ->addMediaCollection(self::MEDIA_TAG_DOCS)
            ->useDisk('s3')
            ->onlyKeepLatest(50);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(self::MEDIA_TAG_THUMB)
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }
}
