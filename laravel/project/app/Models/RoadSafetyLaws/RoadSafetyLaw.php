<?php

namespace App\Models\RoadSafetyLaws;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSafetyLaw extends Model
{
  use HasFactory;

  protected $table = 'road_safety_laws';

  protected $fillable = ['road_safety_law_article_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadSafetyLawTranslation::class,
      'road_safety_law_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      RoadSafetyLawTranslation::class,
      'road_safety_law_id',
      'id'
    );
  }
}
