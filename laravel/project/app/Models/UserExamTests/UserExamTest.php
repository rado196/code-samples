<?php

namespace App\Models\UserExamTests;

use App\Models\ExamTests\ExamTest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamTest extends Model
{
  use HasFactory;

  protected $table = 'user_exam_tests';

  /**
   * The attributes that should be cast.
   *
   * @var array
   */

  protected $casts = [
    'is_completed' => 'boolean',
  ];

  protected $fillable = [
    'user_id',
    'exam_test_id',
    'unique_id',
    'is_completed',
    'finish_time',
  ];

  public function exam_test()
  {
    return $this->hasOne(ExamTest::class, 'id', 'exam_test_id');
  }

  public function user_exam_test_questions()
  {
    return $this->hasMany(
      UserExamTestQuestion::class,
      'user_exam_test_id',
      'id'
    );
  }
}
