<?php

namespace App\Models\RoadMarkings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadMarking extends Model
{
  use HasFactory;

  protected $table = 'road_markings';

  protected $fillable = ['road_marking_article_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadMarkingTranslation::class,
      'road_marking_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      RoadMarkingTranslation::class,
      'road_marking_id',
      'id'
    );
  }
}
