<?php

namespace App\Models\ExamTests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTestAnswer extends Model
{
  use HasFactory;

  protected $table = 'exam_test_answers';

  protected $fillable = [
    'exam_test_id',
    'exam_test_question_id',
    'exam_group_answer_id',
  ];
}
