<?php

namespace App\Models\RoadSigns;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSignArticle extends Model
{
  use HasFactory;

  protected $table = 'road_sign_articles';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      RoadSignArticleTranslation::class,
      'article_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      RoadSignArticleTranslation::class,
      'article_id',
      'id'
    );
  }

  public function road_sign()
  {
    return $this->hasOne(RoadSign::class);
  }
}
