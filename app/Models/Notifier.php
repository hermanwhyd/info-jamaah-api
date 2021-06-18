<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Notifier extends Model
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
        'id', 'name', 'description', 'dueDateAt', 'isRepetition', 'reminderDays', 'lastFiredAt', 'lastFiredStatus', 'lastFiredError'
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
        'isRepetition' => 'boolean',
    ];

    /**
     * The attributes automatically turn into a Carbon object
     *
     * @var array
     */
    protected $dates = [
        'dueDateAt', 'lastFiredAt'
    ];

    public function getReminderDaysAttribute($value)
    {
        return explode(';', $value);
    }

    public function setReminderDaysAttribute($values)
    {
        $this->attributes['reminder_days'] = is_string($values) ? $values : implode(';', $values);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function referable()
    {
        return $this->morphTo();
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscribe');
    }
}
