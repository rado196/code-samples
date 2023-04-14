<?php

namespace App\Models\RoadMarkings;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadMarkingArticleTranslation extends Model
{
  use HasFactory;

  protected $table = 'road_marking_article_translations';

  protected $fillable = ['article_id', 'language_id', 'title'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
