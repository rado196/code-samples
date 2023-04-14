<?php

namespace App\Models\TheoreticalPartTraining;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoreticalPartTrainingPrice extends Model
{
  use HasFactory;

  protected $table = 'theoretical_part_training_prices';

  protected $fillable = ['price'];
}
