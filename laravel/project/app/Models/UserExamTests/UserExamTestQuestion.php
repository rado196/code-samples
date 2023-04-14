<?php

namespace App\Models\UserExamTests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamTestQuestion extends Model
{
  use HasFactory;

  protected $table = 'user_exam_test_questions';

  /**
   * The attributes that should be cast.
   *
   * @var array
   */

  protected $casts = [
    'is_right' => 'boolean',
  ];

  protected $fillable = [
    'user_exam_test_id',
    'exam_test_question_id',
    'exam_test_answer_id',
    'is_right',
  ];
}
