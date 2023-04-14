<?php

namespace App\Models\InstructorInfo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorInfo extends Model
{
  use HasFactory;

  protected $table = 'instructor_infos';

  protected $fillable = ['instructor_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      InstructorInfoTranslation::class,
      'instructor_info_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      InstructorInfoTranslation::class,
      'instructor_info_id',
      'id'
    );
  }
}
