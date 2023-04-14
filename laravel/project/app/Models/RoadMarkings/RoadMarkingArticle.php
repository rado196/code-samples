<?php

namespace App\Models\RoadMarkings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadMarkingArticle extends Model
{
  use HasFactory;

  protected $table = 'road_marking_articles';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadMarkingArticleTranslation::class,
      'article_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      RoadMarkingArticleTranslation::class,
      'article_id',
      'id'
    );
  }

  public function road_marking()
  {
    return $this->hasOne(RoadMarking::class);
  }
}
