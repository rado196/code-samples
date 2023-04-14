<?php

namespace App\Models\MalfunctionList;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MalfunctionListArticle extends Model
{
  use HasFactory;

  protected $table = 'malfunction_list_articles';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      MalfunctionListArticleTranslation::class,
      'article_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      MalfunctionListArticleTranslation::class,
      'article_id',
      'id'
    );
  }

  public function malfunction_list()
  {
    return $this->hasOne(MalfunctionList::class);
  }
}
