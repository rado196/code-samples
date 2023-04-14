<?php

namespace App\Models\RoadSigns;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadSignTranslation extends Model
{
  use HasFactory;

  protected $table = 'road_sign_translations';

  protected $fillable = ['road_sign_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
