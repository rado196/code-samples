<?php

namespace App\Models\TrafficRule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficRuleArticle extends Model
{
  use HasFactory;

  protected $table = 'traffic_rule_articles';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      TrafficRuleArticleTranslation::class,
      'article_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      TrafficRuleArticleTranslation::class,
      'article_id',
      'id'
    );
  }

  public function traffic_rule()
  {
    return $this->hasOne(TrafficRule::class);
  }
}
