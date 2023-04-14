<?php

namespace App\Models\RoadSigns;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSign extends Model
{
  use HasFactory;

  protected $table = 'road_signs';

  protected $fillable = ['road_sign_article_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadSignTranslation::class,
      'road_sign_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(RoadSignTranslation::class, 'road_sign_id', 'id');
  }
}
