<?php

namespace App\Models\TheoreticalPartTraining;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartTrainingVideo extends Model
{
  use HasFactory;

  const VIDEO_SIZES = [
    'xs' => [
      'width' => 426,
      'height' => 240,
    ],
    'sm' => [
      'width' => 640,
      'height' => 360,
    ],
    'md' => [
      'width' => 854,
      'height' => 480,
    ],
    'lg' => [
      'width' => 1280,
      'height' => 720,
    ],
    'xl' => [
      'width' => 1920,
      'height' => 1080,
    ],
  ];

  protected $table = 'theoretical_part_training_videos';

  protected $fillable = ['training_id', 'name', 'poster'];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      TheoreticalPartTrainingVideoTranslation::class,
      'video_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      TheoreticalPartTrainingVideoTranslation::class,
      'video_id',
      'id'
    );
  }
}
