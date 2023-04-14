<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiddenInstructorStudent extends Model
{
  use HasFactory;

  protected $table = 'hidden_instructor_students';

  protected $fillable = ['instructor_id', 'student_id'];
}
