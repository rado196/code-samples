<?php

namespace App\Models;

use App\Models\InstructorInfo\InstructorInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens;
  use HasFactory;
  use Notifiable;
  use SoftDeletes;

  const ROLE_ADMIN = 'admin';
  const ROLE_INSTRUCTOR = 'instructor';
  const ROLE_STUDENT = 'student';

  const PROVIDER_STANDARD = 'standard';
  const PROVIDER_FACEBOOK = 'facebook';
  const PROVIDER_GOOGLE = 'google';
  const PROVIDER_APPLE = 'apple';

  const GENDER_MALE = 'male';
  const GENDER_FEMALE = 'female';

  const PROVIDERS = [
    self::PROVIDER_STANDARD,
    self::PROVIDER_FACEBOOK,
    self::PROVIDER_GOOGLE,
    self::PROVIDER_APPLE,
  ];

  const GENDERS = [self::GENDER_MALE, self::GENDER_FEMALE];

  protected $fillable = [
    'provider',
    'role',
    'verified_at',
    'avatar',
    'first_name',
    'last_name',
    'gender',
    'email',
    'phone',
    'password',
    'apple_id',
    'facebook_id',
    'google_id',
  ];

  protected $hidden = ['password'];

  protected $appends = ['rating'];

  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function wallet()
  {
    return $this->hasOne(Wallet::class);
  }

  public function instructor_work_detail()
  {
    return $this->hasOne(InstructorWorkDetail::class, 'instructor_id');
  }

  public function instructor_info()
  {
    return $this->hasOne(InstructorInfo::class, 'instructor_id');
  }

  public function instructor_day_offs()
  {
    return $this->hasMany(InstructorDayOff::class, 'instructor_id');
  }

  public function instructor_ratings()
  {
    return $this->hasMany(InstructorRating::class, 'instructor_id');
  }

  public function getRatingAttribute()
  {
    return round($this->instructor_ratings()->average('rating'), 2);
  }

  public function hidden_instructor_students()
  {
    return $this->hasManyThrough(
      User::class,
      HiddenInstructorStudent::class,
      'instructor_id',
      'id',
      'id',
      'student_id'
    );
  }

  public function student_hidden_instructors()
  {
    return $this->hasManyThrough(
      User::class,
      HiddenInstructorStudent::class,
      'student_id',
      'id',
      'id',
      'instructor_id'
    );
  }
}
