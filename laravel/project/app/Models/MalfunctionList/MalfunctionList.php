<?php

namespace App\Models\MalfunctionList;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MalfunctionList extends Model
{
  use HasFactory;

  protected $table = 'malfunction_lists';

  protected $fillable = ['malfunction_list_article_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      MalfunctionListTranslation::class,
      'malfunction_list_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      MalfunctionListTranslation::class,
      'malfunction_list_id',
      'id'
    );
  }
}
