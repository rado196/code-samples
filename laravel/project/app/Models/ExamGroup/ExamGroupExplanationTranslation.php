<?php

namespace App\Models\ExamGroup;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamGroupExplanationTranslation extends Model
{
  use HasFactory;

  protected $table = 'exam_group_explanation_translations';

  protected $fillable = [
    'explanation_id',
    'language_id',
    'title',
    'description',
  ];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
