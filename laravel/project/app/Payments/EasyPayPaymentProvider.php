<?php

namespace App\Payments;

use App\Contracts\Payments\BasePaymentProvider;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EasyPayPaymentProvider extends BasePaymentProvider
{
  /** @override */
  protected function getProviderName(): string
  {
    return Payment::PROVIDER_EASYPAY;
  }

  /** @override */
  protected function getReturnUrlKey(): string
  {
    return Payment::RETURN_URL_KEY_EASYPAY;
  }

  /** @override */
  protected function isTestMode(): bool
  {
    $env = config('custom.payments.easypay.mode');
    return $this->checkEnvIsTest($env);
  }

  /** @override */
  protected function getApiHost(): string
  {
    // EasyPay not api host.

    return '';
  }

  /** @override */
  protected function refund(Payment $payment)
  {
    // EasyPay does not support refund action.
  }

  /** @override */
  protected function complete(Payment $payment)
  {
    // EasyPay automatically make payments completed.
  }

  private $currencyCodes = [
    'AMD' => Payment::CURRENCY_AMD,
    'EUR' => Payment::CURRENCY_EUR,
    'USD' => Payment::CURRENCY_USD,
    'RUB' => Payment::CURRENCY_RUB,
  ];

  private $languageCodes = [
    Payment::LANGUAGE_ARM => 'hy',
    Payment::LANGUAGE_RUS => 'ru',
    Payment::LANGUAGE_ENG => 'en',
  ];

  private $responseMessages = [
    'Info.UserID' => [
      Payment::LANGUAGE_ARM => 'User ID',
      Payment::LANGUAGE_RUS => 'User ID',
      Payment::LANGUAGE_ENG => 'User ID',
    ],
    'Info.UserName' => [
      Payment::LANGUAGE_ARM => 'Օգտատեր',
      Payment::LANGUAGE_RUS => 'Имя пользователя',
      Payment::LANGUAGE_ENG => 'User name',
    ],
    'Info.PhoneNumber' => [
      Payment::LANGUAGE_ARM => 'Հեռախոս',
      Payment::LANGUAGE_RUS => 'Номер телефона',
      Payment::LANGUAGE_ENG => 'Phone number',
    ],
    'Info.PaymentCode' => [
      Payment::LANGUAGE_ARM => 'Վճարման կոդ',
      Payment::LANGUAGE_RUS => 'Код платежа',
      Payment::LANGUAGE_ENG => 'Payment code',
    ],
    'ActionAllowed' => [
      Payment::LANGUAGE_ARM => 'Գործողությունը հաստատված է:',
      Payment::LANGUAGE_RUS => 'Действие разрешено.',
      Payment::LANGUAGE_ENG => 'Action allowed.',
    ],
    'IncorrectParams' => [
      Payment::LANGUAGE_ARM => 'Կան սխալ պարամետրեր:',
      Payment::LANGUAGE_RUS => 'Есть неверные параметры',
      Payment::LANGUAGE_ENG => 'There are incorrect parameters provided.',
    ],
    'WrongIdentifier' => [
      Payment::LANGUAGE_ARM => 'Օգտագործողի նույնականացումը ձախողվեց:',
      Payment::LANGUAGE_RUS =>
        'Не удалось найти пользователя с данным идентификатором.',
      Payment::LANGUAGE_ENG => 'Could not find a user with a given identifier.',
    ],
    'PaymentNotAccepted' => [
      Payment::LANGUAGE_ARM => 'Վճարումը չի ընդունվել:',
      Payment::LANGUAGE_RUS => 'Оплата не принимается.',
      Payment::LANGUAGE_ENG => 'Payment is not accepted.',
    ],
    'PaymentAccepted' => [
      Payment::LANGUAGE_ARM => 'Վճարումը հաջողությամբ ավարտվեց:',
      Payment::LANGUAGE_RUS => 'Оплата успешно завершена.',
      Payment::LANGUAGE_ENG => 'Payment successfully completed.',
    ],
  ];

  private function cacheLanguage(array $inputs, $lang = null)
  {
    $key = 'EasyPay-' . implode(',', $inputs);
    if (!is_null($lang)) {
      Cache::put($key, $lang, 120);
    }

    $lang = Cache::get($key);
    foreach ($this->languageCodes as $code => $langName) {
      if ($langName === $lang) {
        $this->setLanguage($code);
      }
    }

    return $lang;
  }

  private function messageByName($name)
  {
    $lang = $this->getLanguage();
    if (
      isset($this->responseMessages[$name]) &&
      isset($this->responseMessages[$name][$lang])
    ) {
      return $this->responseMessages[$name][$lang];
    }

    return $name;
  }

  private function detectUserByIdentifier($identifiers)
  {
    $userQueryBuilder = User::query();
    foreach ($identifiers as $identifier) {
      if (is_null($identifier) || empty($identifier)) {
        continue;
      }

      if (strlen($identifier) === 7) {
        $userQueryBuilder->whereId((int) $identifier);
      } else {
        $phoneNumberWithoutZero = substr($identifier, 1);
        $possiblePhoneNumbers = [
          $identifier,
          '00374' . $phoneNumberWithoutZero,
          '+374' . $phoneNumberWithoutZero,
          '374' . $phoneNumberWithoutZero,
        ];

        $userQueryBuilder->whereIn('phone', $possiblePhoneNumbers);
      }
    }

    $user = $userQueryBuilder->first();
    if (!is_null($user)) {
      Auth::setUser($user);
    }
  }

  private function respondWithCode(
    $code,
    $message,
    $appendArgs,
    ...$checksumData
  ) {
    $responseData = [
      'ResponseMessage' => $this->messageByName($message),
      'ResponseCode' => $code,
      'Debt' => 0.0,
      'Checksum' => $this->makeChecksum(...$checksumData),
    ];

    return array_merge($responseData, $appendArgs);
  }

  private function respondWithError($code, $message, ...$checksumData)
  {
    $propertyList = [];
    if (empty($checksumData)) {
      $checksumData = ['[]'];
    }

    return $this->respondWithCode(
      $code,
      $message,
      ['PropertyList' => $propertyList],
      ...$checksumData
    );
  }

  private function respondWithSuccess($message, $appendArgs, ...$checksumData)
  {
    return $this->respondWithCode(0, $message, $appendArgs, ...$checksumData);
  }

  private function makeChecksum(...$data)
  {
    $token = config('custom.payments.easypay.token');
    $dataToHash = $token . implode('', $data);

    $checksum = strtolower(md5($dataToHash));
    return $checksum;
  }

  private function validateChecksum($providedChecksum, ...$data)
  {
    $localChecksum = $this->makeChecksum(...$data);
    return strtolower($providedChecksum) === $localChecksum;
  }

  /**
   * Response to provider.
   */
  public function checking($details)
  {
    $checkSum = $details['Checksum'];
    $inputs = $details['Inputs'];
    $lang = $details['Lang'];
    // $currency = $details['Currency'];

    $this->cacheLanguage($inputs, $lang);

    if (!$this->validateChecksum($checkSum, implode('', $inputs), $lang)) {
      return $this->respondWithError(1, 'IncorrectParams');
    }

    $this->detectUserByIdentifier($inputs);
    if (is_null($this->getUser())) {
      return $this->respondWithError(2, 'WrongIdentifier');
    }

    $propertyList = [
      [
        'key' => $this->messageByName('Info.UserName'),
        'value' =>
          $this->getUser()->first_name . ' ' . $this->getUser()->last_name,
      ],
      [
        'key' => $this->messageByName('Info.PhoneNumber'),
        'value' => $this->getUser()->phone,
      ],
    ];

    return $this->respondWithSuccess(
      'ActionAllowed',
      ['PropertyList' => $propertyList],
      json_encode($propertyList, JSON_UNESCAPED_UNICODE)
    );
  }

  /**
   * Initialize payment.
   */
  public function initialize($details)
  {
    $checkSum = $details['Checksum'];
    $inputs = $details['Inputs'];
    $amount = $details['Amount'];
    $transactionID = $details['TransactID'];
    $dtTime = $details['DtTime'];
    $currency = $details['Currency'];

    $this->detectUserByIdentifier($inputs);
    $this->cacheLanguage($inputs);
    $this->setCurrency($this->currencyCodes[$currency]);

    if (
      !$this->validateChecksum(
        $checkSum,
        implode('', $inputs),
        $amount,
        $transactionID
      )
    ) {
      return $this->respondWithError(3, 'PaymentNotAccepted', $dtTime);
    }

    $this->setProviderTransactionId($transactionID);

    $payment = $this->initializePayment();
    if ($payment->status == Payment::STATUS_PENDING) {
      $payment->status = Payment::STATUS_SUCCESS;
      $this->completeTransaction($payment);
    } else {
      $payment->status = Payment::STATUS_FAILURE;
    }

    $payment->save();

    $propertyList = [
      [
        'key' => $this->messageByName('Info.PaymentCode'),
        'value' => (string) $payment->order_id,
      ],
    ];

    $createdAt = $payment->created_at->format('Y-m-d\TH:i:s');

    return $this->respondWithSuccess(
      'PaymentAccepted',
      [
        'DtTime' => $createdAt,
        'PropertyList' => $propertyList,
      ],
      $createdAt
    );
  }
}
