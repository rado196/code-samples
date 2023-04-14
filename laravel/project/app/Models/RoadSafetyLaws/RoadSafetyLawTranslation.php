<?php

namespace App\Models\RoadSafetyLaws;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSafetyLawTranslation extends Model
{
  use HasFactory;

  protected $table = 'road_safety_law_translations';

  protected $fillable = ['road_safety_law_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
