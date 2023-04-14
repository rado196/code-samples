<?php

namespace App\Models\ExamTests;

use App\Models\ExamGroup\ExamGroupAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTestQuestion extends Model
{
  use HasFactory;

  protected $table = 'exam_test_questions';

  protected $fillable = [
    'exam_test_id',
    'exam_group_question_id',
    'explanation_id',
  ];

  protected $with = ['answers'];

  public function answers()
  {
    return $this->belongsToMany(ExamGroupAnswer::class, 'exam_test_answers');
  }
}
