<?php

namespace App\Models\InstructorInfo;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorInfoTranslation extends Model
{
  use HasFactory;

  protected $table = 'instructor_info_translations';

  protected $fillable = ['instructor_info_id', 'language_id', 'description'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
