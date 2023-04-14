<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorRating extends Model
{
  use HasFactory;

  protected $table = 'instructor_ratings';

  protected $fillable = ['instructor_id', 'student_id', 'rating', 'comment'];

  public function student()
  {
    return $this->hasOne(User::class, 'id', 'student_id');
  }
}
