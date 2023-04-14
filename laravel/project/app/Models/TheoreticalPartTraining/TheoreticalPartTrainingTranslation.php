<?php

namespace App\Models\TheoreticalPartTraining;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartTrainingTranslation extends Model
{
  use HasFactory;

  protected $table = 'theoretical_part_training_translations';

  protected $fillable = ['training_id', 'language_id', 'title', 'description'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
