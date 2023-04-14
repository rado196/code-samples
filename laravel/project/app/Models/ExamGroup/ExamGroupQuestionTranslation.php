<?php

namespace App\Models\ExamGroup;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGroupQuestionTranslation extends Model
{
  use HasFactory;

  protected $table = 'exam_group_question_translations';

  protected $fillable = ['exam_group_question_id', 'title', 'language_id'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
