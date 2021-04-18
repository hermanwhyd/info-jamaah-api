<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class Residance extends Model
{
  use CamelCasing;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'typeEnum',
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
    'createdAt',
    'updatedAt',
  ];

  public function address()
  {
    return $this->morphOne(Address::class, 'addressable');
  }

  public function type()
  {
    return $this->belongsTo(Enum::class, 'type_enum', 'code')->whereGroup('RESIDANCE_TYPE');
  }

  public function occupies()
  {
    return $this->hasMany(Family::class);
  }
}
