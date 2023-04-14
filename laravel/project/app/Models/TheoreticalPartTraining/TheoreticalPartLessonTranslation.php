<?php

namespace App\Models\TheoreticalPartTraining;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartLessonTranslation extends Model
{
  use HasFactory;

  protected $table = 'theoretical_part_lesson_translations';

  protected $fillable = ['lesson_id', 'language_id', 'title', 'description'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
