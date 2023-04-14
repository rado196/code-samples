<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAppointment extends Model
{
  use HasFactory;

  protected $table = 'student_appointments';

  const STATUS_PENDING = 'pending';
  const STATUS_BOOKED = 'booked';
  const STATUS_COMPLETED = 'completed';
  const STATUS_EXPIRED = 'expired';
  const STATUS_CANCELED = 'canceled';

  const APPOINTMENT_STATUSES = [
    self::STATUS_PENDING,
    self::STATUS_BOOKED,
    self::STATUS_COMPLETED,
    self::STATUS_EXPIRED,
    self::STATUS_CANCELED,
  ];

  protected $with = ['student', 'instructor'];

  protected $fillable = [
    'student_id',
    'instructor_id',
    'date',
    'start_time',
    'end_time',
    'duration',
    'price',
    'status',
  ];

  public function student()
  {
    return $this->hasOne(User::class, 'id', 'student_id');
  }

  public function instructor()
  {
    return $this->hasOne(User::class, 'id', 'instructor_id');
  }
}
