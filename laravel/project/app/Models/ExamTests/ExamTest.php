<?php

namespace App\Models\ExamTests;

use App\Models\ExamGroup\ExamGroupQuestion;
use App\Models\UserExamTests\UserExamTest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamTest extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'exam_tests';

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'is_valid' => 'boolean',
  ];

  protected $fillable = ['is_valid', 'duration', 'max_wrong_answers'];

  protected $with = ['translation', 'questions'];

  public function translation()
  {
    $language = request()->attributes->get('lang');

    return $this->hasOne(
      ExamTestTranslation::class,
      'exam_test_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(ExamTestTranslation::class, 'exam_test_id', 'id');
  }

  public function questions()
  {
    return $this->belongsToMany(
      ExamGroupQuestion::class,
      'exam_test_questions'
    )->with(['answers', 'translation', 'explanation']);
  }

  public function user_exam_tests()
  {
    return $this->hasMany(UserExamTest::class, 'exam_test_id', 'id');
  }
}
