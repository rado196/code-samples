<?php

namespace App\Models\VehicleSigns;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSign extends Model
{
  use HasFactory;

  protected $table = 'vehicle_signs';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      VehicleSignTranslation::class,
      'vehicle_sign_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      VehicleSignTranslation::class,
      'vehicle_sign_id',
      'id'
    );
  }
}
