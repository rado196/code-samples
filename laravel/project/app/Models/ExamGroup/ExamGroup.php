<?php

namespace App\Models\ExamGroup;

use App\Models\ExamGroup\QuestionCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamGroup extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'exam_groups';

  protected $fillable = ['question_category_id'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      ExamGroupTranslation::class,
      'exam_group_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(ExamGroupTranslation::class, 'exam_group_id', 'id');
  }

  public function category()
  {
    return $this->hasOne(
      QuestionCategory::class,
      'id',
      'question_category_id'
    )->with('translation');
  }

  public function questions()
  {
    return $this->hasMany(ExamGroupQuestion::class, 'exam_group_id', 'id');
  }
}
