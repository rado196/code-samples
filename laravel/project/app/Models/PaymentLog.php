<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
  protected $table = 'payment_logs';

  protected $casts = [
    'request_body' => 'object',
    'request_headers' => 'object',
    'response_body' => 'object',
    'response_headers' => 'object',
  ];

  protected $fillable = [
    'user_id',
    'url',
    'http_method',
    'request_body',
    'request_headers',
    'response_body',
    'response_headers',
    'status_code',
  ];
}
