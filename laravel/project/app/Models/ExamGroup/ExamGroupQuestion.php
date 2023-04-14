<?php

namespace App\Models\ExamGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGroupQuestion extends Model
{
  use HasFactory;

  protected $table = 'exam_group_questions';

  protected $fillable = ['image', 'exam_group_id'];

  // private $with = ['translation'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      ExamGroupQuestionTranslation::class,
      'exam_group_question_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      ExamGroupQuestionTranslation::class,
      'exam_group_question_id',
      'id'
    );
  }

  public function answers()
  {
    return $this->hasMany(ExamGroupAnswer::class, 'question_id', 'id')->with(
      'translation'
    );
  }

  public function explanation()
  {
    return $this->hasOne(
      ExamGroupExplanation::class,
      'question_id',
      'id'
    )->with('translation', 'translations');
  }
}
