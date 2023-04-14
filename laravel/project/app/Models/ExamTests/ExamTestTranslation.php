<?php

namespace App\Models\ExamTests;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTestTranslation extends Model
{
  use HasFactory;

  protected $table = 'exam_test_translations';

  protected $fillable = ['exam_test_id', 'title', 'language_id'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
