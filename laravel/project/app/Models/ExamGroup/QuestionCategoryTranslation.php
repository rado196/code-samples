<?php

namespace App\Models\ExamGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCategoryTranslation extends Model
{
  use HasFactory;

  protected $table = 'question_category_translations';

  protected $fillable = ['question_category_id', 'title', 'language_id'];
}
