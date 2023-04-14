<?php

namespace App\Models\RoadSafetyLaws;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSafetyLawArticles extends Model
{
  use HasFactory;

  protected $table = 'road_safety_law_articles';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadSafetyLawArticleTranslation::class,
      'article_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      RoadSafetyLawArticleTranslation::class,
      'article_id',
      'id'
    );
  }

  public function road_safety_law()
  {
    return $this->hasOne(
      RoadSafetyLaw::class,
      'road_safety_law_article_id',
      'id'
    );
  }
}
