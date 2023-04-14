<?php

namespace App\Models\TrafficRule;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficRuleTranslation extends Model
{
  use HasFactory;

  protected $table = 'traffic_rule_translations';

  protected $fillable = ['traffic_rule_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
