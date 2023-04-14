<?php

namespace App\Models\MalfunctionList;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MalfunctionListTranslation extends Model
{
  use HasFactory;

  protected $table = 'malfunction_list_translations';

  protected $fillable = ['malfunction_list_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
