<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Eloquence\Behaviours\CamelCasing;

class JamaahPembinaan extends Model
{
  use CamelCasing;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'jamaahId', 'pembinaEnum', 'lvPembinaanEnum', 'startDate', 'endDate'
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
    'jamaahId', 'int'
  ];

  /**
   * The attributes automatically turn into a Carbon object
   *
   * @var array
   */
  protected $dates = [
    'startDate',
    'endDate',
  ];

  public function scopeActive($query)
  {
    return $query->whereNull('end_date');
  }

  public function pembina()
  {
    return $this->belongsTo(Enum::class, 'pembina_enum', 'code')->where('group', 'like', 'PEMBINA_%');
  }

  public function lvPembina()
  {
    return $this->belongsTo(Enum::class, 'lv_pembinaan_enum', 'code')->whereGroup('LV_PEMBINAAN');
  }
}
