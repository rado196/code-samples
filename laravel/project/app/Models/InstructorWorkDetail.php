<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorWorkDetail extends Model
{
  use HasFactory;

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'hidden' => 'boolean',
    'monday' => 'boolean',
    'tuesday' => 'boolean',
    'wednesday' => 'boolean',
    'thursday' => 'boolean',
    'friday' => 'boolean',
    'saturday' => 'boolean',
    'sunday' => 'boolean',
  ];

  protected $table = 'instructor_work_details';

  protected $fillable = [
    'instructor_id',
    'driving_course_hourly_price',
    'hidden',
    'monday',
    'wednesday',
    'thursday',
    'friday',
    'saturday',
    'sunday',
    'tuesday',
    'monday_start_time',
    'monday_end_time',
    'tuesday_start_time',
    'tuesday_end_time',
    'wednesday_start_time',
    'wednesday_end_time',
    'thursday_start_time',
    'thursday_end_time',
    'friday_start_time',
    'friday_end_time',
    'saturday_start_time',
    'saturday_end_time',
    'sunday_start_time',
    'sunday_end_time',
  ];
}
