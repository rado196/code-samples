<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTheoreticalPartTraining extends Model
{
  use HasFactory;

  protected $table = 'student_theoretical_part_trainings';

  const STATUS_PENDING = 'pending';
  const STATUS_PAID = 'paid';
  const STATUS_EXPIRED = 'expired';
  const STATUS_CANCELED = 'canceled';

  const TRAINING_STATUSES = [
    self::STATUS_PENDING,
    self::STATUS_PAID,
    self::STATUS_EXPIRED,
    self::STATUS_CANCELED,
  ];

  const EXPIRATION_DATE = '30'; // days

  protected $fillable = ['student_id', 'status', 'expiration_date'];
}
