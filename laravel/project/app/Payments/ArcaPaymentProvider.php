<?php

namespace App\Payments;

use App\Contracts\Payments\BasePaymentProvider;
use App\Models\Payment;

class ArcaPaymentProvider extends BasePaymentProvider
{
  /** @override */
  protected function getProviderName(): string
  {
    return Payment::PROVIDER_ARCA;
  }

  /** @override */
  protected function getReturnUrlKey(): string
  {
    return Payment::RETURN_URL_KEY_ARCA;
  }

  /** @override */
  protected function isTestMode(): bool
  {
    $env = config('custom.payments.arca.mode');
    return $this->checkEnvIsTest($env);
  }

  /** @override */
  protected function getApiHost(): string
  {
    if ($this->isTestMode()) {
      return 'https://ipaytest.arca.am:8445/payment/rest';
    }

    return 'https://ipay.arca.am/payment/rest';
  }

  /** @override */
  protected function refund(Payment $payment)
  {
    // @TODO: write refund logic
  }

  /** @override */
  protected function complete(Payment $payment)
  {
    // ARCA automatically make payments completed.
  }

  private $currencyCodes = [
    Payment::CURRENCY_AMD => '051',
    Payment::CURRENCY_EUR => '978',
    Payment::CURRENCY_USD => '840',
    Payment::CURRENCY_RUB => '643',
  ];

  private $languageCodes = [
    Payment::LANGUAGE_ARM => 'hy',
    Payment::LANGUAGE_RUS => 'ru',
    Payment::LANGUAGE_ENG => 'en',
  ];

  /**
   * Initialize payment.
   */
  public function initialize()
  {
    $payment = $this->initializePayment();

    $initializeUrl = $this->buildUrl('/register.do');
    $initializeData = [
      'userName' => config('custom.payments.arca.username'),
      'password' => config('custom.payments.arca.password'),
      'orderNumber' => $this->getOrderId(),
      'amount' => $this->getAmount() * 100,
      'currency' => $this->currencyCodes['AMD'],
      'description' => $this->getDescription(),
      'language' => $this->languageCodes[$this->getLanguage()],
      'returnUrl' => $this->buildReturnUrl(),
      'sessionTimeoutSecs' => 600, // 10 minute
      'jsonParams' => json_encode([
        'FORCE_3DS2' => 'true',
        'payment_id' => $payment->id,
        'transaction_id' => $this->getTransactionId(),
        'order_id' => $this->getOrderId(),
        'user_id' => $this->getUserId(),
        'wallet_id' => $this->getWalletId(),
      ]),
    ];

    $response = $this->http()->post(
      $initializeUrl,
      $initializeData,
      [],
      [
        'content-type' => 'multipart/form-data',
      ]
    );

    $responseData = $response->getResponseContent();
    if (is_null($responseData)) {
      $payment->provider_response_message =
        'Merchant provider does not respond.';
      $payment->status = Payment::STATUS_FAILURE;
      $payment->save();
      $this->log(
        '[Initialize payment] ' . $payment->provider_response_message,
        [
          'order_id' => $this->getOrderId(),
          'user_id' => $this->getUserId(),
          'transaction_id' => $this->getTransactionId(),
        ]
      );

      return $this->respondError(
        'Error: ' . $payment->provider_response_message
      );
    }

    $payment->provider_transaction_id = $responseData['orderId'];
    $payment->provider_response_message =
      'Error: ' . $responseData['errorCodeString'];
    $payment->provider_response_code = $responseData['errorCode'];

    if ($responseData['errorCode'] != 0) {
      $payment->status = Payment::STATUS_FAILURE;
      $payment->save();

      $this->log('[Initialize payment] ' . $responseData['errorCodeString'], [
        'order_id' => $this->getOrderId(),
        'user_id' => $this->getUserId(),
        'transaction_id' => $this->getTransactionId(),
      ]);

      return $this->respondError(
        'Error: ' . $responseData['errorCodeString'],
        $responseData['errorCode']
      );
    } else {
      $payment->status = Payment::STATUS_PENDING;
      $payment->save();

      $redirectUrl = $responseData['formUrl'];
      if ($this->isTestMode()) {
        $patterns = [
          str_replace('_api', '', config('custom.payments.arca.username')),
          config('custom.payments.arca.username'),
        ];

        $redirectUrl = str_replace($patterns, 'test', $redirectUrl);
      }

      return $this->respondRedirect($redirectUrl);
    }
  }

  /**
   * Confirm payment.
   */
  public function confirm($details)
  {
    $providerPaymentId = $details['orderId'];
    $payment = $this->findPaymentByProviderId($providerPaymentId);

    if (is_null($payment)) {
      $this->logState('Payment not found!', $details);
      return Payment::STATUS_FAILURE;
    }

    if ($payment->status == Payment::STATUS_PENDING) {
      $loadInfoUrl = $this->buildUrl('/getOrderStatusExtended.do');
      $loadInfoData = [
        'userName' => config('custom.payments.arca.username'),
        'password' => config('custom.payments.arca.password'),
        'orderNumber' => $payment->order_id,
        'orderId' => $providerPaymentId,
        'language' => $this->languageCodes[Payment::LANGUAGE_ENG],
      ];

      $response = $this->http()->post(
        $loadInfoUrl,
        $loadInfoData,
        [],
        [
          'content-type' => 'multipart/form-data',
        ]
      );

      $responseData = $response->getResponseContent();
      if (!is_null($responseData)) {
        if (
          isset($responseData['orderStatus']) &&
          2 == $responseData['orderStatus']
        ) {
          $payment->status = Payment::STATUS_SUCCESS;
          $this->logState('Payment successfully confirmed', $details);
          $this->completeTransaction($payment);
        } else {
          $payment->status = Payment::STATUS_FAILURE;
          $this->logState('Payment failed!', $details);
        }

        $payment->provider_description = $responseData['actionCodeDescription'];
      } else {
        // dd($response);
      }

      $payment->save();
    }

    return $payment->status;
  }

  private function logState($message, $details)
  {
    if (isset($details['orderId'])) {
      $debuggingData = [
        'order_id' => $details['orderId'],
      ];

      $this->log('[Confirm payment] ' . $message, $debuggingData);
    }
  }
}
