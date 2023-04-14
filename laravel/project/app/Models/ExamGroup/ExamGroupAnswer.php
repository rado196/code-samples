<?php

namespace App\Models\ExamGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGroupAnswer extends Model
{
  use HasFactory;

  /**
   * The attributes that should be cast.
   *
   * @var array
   */

  protected $casts = [
    'is_right' => 'boolean',
  ];

  protected $table = 'exam_group_answers';

  protected $fillable = ['is_right', 'exam_group_id', 'question_id'];

  protected $with = ['translation'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      ExamGroupAnswerTranslation::class,
      'exam_group_answer_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      ExamGroupAnswerTranslation::class,
      'exam_group_answer_id',
      'id'
    );
  }
}
