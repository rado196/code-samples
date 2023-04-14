<?php

namespace App\Models\ExamGroup;

use App\Models\ExamGroup\QuestionCategoryTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
  use HasFactory;

  protected $table = 'question_categories';

  protected $fillable = ['slug'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      QuestionCategoryTranslation::class,
      'question_category_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      QuestionCategoryTranslation::class,
      'question_category_id',
      'id'
    );
  }
}
