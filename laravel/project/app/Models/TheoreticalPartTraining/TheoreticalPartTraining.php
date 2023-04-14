<?php

namespace App\Models\TheoreticalPartTraining;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartTraining extends Model
{
  use HasFactory;

  protected $table = 'theoretical_part_trainings';

  protected $fillable = ['lesson_id'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      TheoreticalPartTrainingTranslation::class,
      'training_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      TheoreticalPartTrainingTranslation::class,
      'training_id',
      'id'
    );
  }

  public function videos()
  {
    return $this->hasMany(
      TheoreticalPartTrainingVideo::class,
      'training_id',
      'id'
    );
  }
}
