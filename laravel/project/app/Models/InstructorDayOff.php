<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorDayOff extends Model
{
  use HasFactory;

  protected $table = 'instructor_day_offs';

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'is_full_day' => 'boolean',
  ];

  protected $fillable = [
    'instructor_id',
    'date',
    'is_full_day',
    'start_time',
    'end_time',
  ];
}
