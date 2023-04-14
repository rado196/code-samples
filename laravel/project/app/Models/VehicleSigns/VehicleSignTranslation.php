<?php

namespace App\Models\VehicleSigns;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSignTranslation extends Model
{
  use HasFactory;

  protected $table = 'vehicle_sign_translations';

  protected $fillable = ['vehicle_sign_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
