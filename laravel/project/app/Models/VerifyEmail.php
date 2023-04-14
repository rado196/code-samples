<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyEmail extends Model
{
  protected $table = 'verify_emails';

  protected $fillable = ['user_id', 'verify_token'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
