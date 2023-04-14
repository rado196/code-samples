<?php

namespace App\Models\ExamGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGroupExplanation extends Model
{
  use HasFactory;

  protected $table = 'exam_group_explanations';

  protected $fillable = ['exam_group_id', 'question_id'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      ExamGroupExplanationTranslation::class,
      'explanation_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      ExamGroupExplanationTranslation::class,
      'explanation_id',
      'id'
    );
  }
}
