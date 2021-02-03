<?php

namespace App\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
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
    'contactType', 'label', 'value',
  ];

  public function contactable()
  {
    return $this->morphTo();
  }
}
