<?php

namespace App\Models\TrafficRule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficRule extends Model
{
  use HasFactory;

  protected $table = 'traffic_rules';

  protected $fillable = ['traffic_rule_article_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      TrafficRuleTranslation::class,
      'traffic_rule_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      TrafficRuleTranslation::class,
      'traffic_rule_id',
      'id'
    );
  }
}
