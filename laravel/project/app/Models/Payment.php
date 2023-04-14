<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  const PROVIDER_AMERIA_BANK = 'AmeriaBank';
  const PROVIDER_ARCA = 'ArCa';
  const PROVIDER_IDRAM = 'IDram';
  const PROVIDER_EASYPAY = 'EasyPay';

  const RETURN_URL_KEY_AMERIA_BANK = 'ameria-bank';
  const RETURN_URL_KEY_ARCA = 'arca';
  const RETURN_URL_KEY_IDRAM = 'idram';
  const RETURN_URL_KEY_EASYPAY = 'easy-pay';

  const LANGUAGE_ARM = 'arm';
  const LANGUAGE_RUS = 'rus';
  const LANGUAGE_ENG = 'eng';

  const CURRENCY_AMD = 'AMD';
  const CURRENCY_RUB = 'RUB';
  const CURRENCY_USD = 'USD';
  const CURRENCY_EUR = 'EUR';

  const STATUS_PENDING = 'pending';
  const STATUS_SUCCESS = 'success';
  const STATUS_FAILURE = 'failure';
  const STATUS_REFUNDED = 'refunded';
  const STATUS_EXPIRED = 'expired';

  protected $table = 'payments';

  protected $fillable = [
    'provider',
    'user_id',
    'wallet_id',
    'amount',
    'bonus',
    'amount_with_bonus',
    'currency',
    'status',
    'description',
    'language',
    'order_id',
    'transaction_id',
    'provider_transaction_id',
    'provider_response_code',
    'provider_response_message',
    'provider_description',
  ];

  public function student()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
