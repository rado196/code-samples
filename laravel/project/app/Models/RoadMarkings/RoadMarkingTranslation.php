<?php

namespace App\Models\RoadMarkings;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadMarkingTranslation extends Model
{
  use HasFactory;

  protected $table = 'road_marking_translations';

  protected $fillable = ['road_marking_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
