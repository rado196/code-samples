<?php

namespace App\Models\TheoreticalPartTraining;

use App\Models\ExamGroup\ExamGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartLesson extends Model
{
  use HasFactory;

  protected $table = 'theoretical_part_lessons';

  protected $fillable = ['exam_group_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      TheoreticalPartLessonTranslation::class,
      'lesson_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function examGroup()
  {
    return $this->hasOne(ExamGroup::class, 'id', 'exam_group_id');
  }

  public function translations()
  {
    return $this->hasMany(
      TheoreticalPartLessonTranslation::class,
      'lesson_id',
      'id'
    );
  }

  public function trainings()
  {
    return $this->hasMany(TheoreticalPartTraining::class, 'lesson_id', 'id');
  }
}
