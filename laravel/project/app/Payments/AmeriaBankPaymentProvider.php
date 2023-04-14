<?php

namespace App\Payments;

use App\Contracts\Payments\BasePaymentProvider;
use App\Models\Payment;

class AmeriaBankPaymentProvider extends BasePaymentProvider
{
  /** @override */
  protected function getProviderName(): string
  {
    return Payment::PROVIDER_AMERIA_BANK;
  }

  /** @override */
  protected function getReturnUrlKey(): string
  {
    return Payment::RETURN_URL_KEY_AMERIA_BANK;
  }

  /** @override */
  protected function isTestMode(): bool
  {
    $env = config('custom.payments.ameriabank.mode');
    return $this->checkEnvIsTest($env);
  }

  /** @override */
  protected function getApiHost(): string
  {
    if ($this->isTestMode()) {
      return 'https://servicestest.ameriabank.am';
    }

    return 'https://services.ameriabank.am';
  }

  /** @override */
  protected function refund(Payment $payment)
  {
    $refundUrl = $this->buildUrl('/VPOS/api/VPOS/RefundPayment');
    $refundData = [
      'ClientID' => config('custom.payments.ameriabank.client_id'),
      'Username' => config('custom.payments.ameriabank.username'),
      'Password' => config('custom.payments.ameriabank.password'),
      'Amount' => $payment->amount,
      'PaymentID' => $payment->provider_transaction_id,
    ];

    $this->http()->post($refundUrl, $refundData);
  }

  /** @override */
  protected function complete(Payment $payment)
  {
    return $this->getDetails($payment);
  }

  /**
   * Get payment details.
   *
   * @summary We need to call get details to make payment confirmation
   *          in AmeriaBank.
   */
  private function getDetails(Payment $payment)
  {
    $initializeUrl = $this->buildUrl('/VPOS/api/VPOS/GetPaymentDetails');
    $initializeData = [
      'Username' => config('custom.payments.ameriabank.username'),
      'Password' => config('custom.payments.ameriabank.password'),
      'PaymentID' => $payment->provider_transaction_id,
    ];

    return $this->http()->post($initializeUrl, $initializeData);
  }

  private $currencyCodes = [
    Payment::CURRENCY_AMD => '051',
    Payment::CURRENCY_EUR => '978',
    Payment::CURRENCY_USD => '840',
    Payment::CURRENCY_RUB => '643',
  ];

  private $languageCodes = [
    Payment::LANGUAGE_ARM => 'am',
    Payment::LANGUAGE_RUS => 'ru',
    Payment::LANGUAGE_ENG => 'en',
  ];

  /**
   * Get amount based environment
   *
   * @summary In test mode we need to provide amount=10 only.
   */
  private function getAmountModeBased()
  {
    if ($this->isTestMode()) {
      return 10;
    }

    return $this->getAmount();
  }

  /**
   * Initialize payment.
   */
  public function initialize()
  {
    $payment = $this->initializePayment();

    $initializeUrl = $this->buildUrl('/VPOS/api/VPOS/InitPayment');
    $initializeData = [
      'ClientID' => config('custom.payments.ameriabank.client_id'),
      'Username' => config('custom.payments.ameriabank.username'),
      'Password' => config('custom.payments.ameriabank.password'),
      'Amount' => $this->getAmountModeBased(),
      'Currency' => $this->currencyCodes['AMD'],
      'Description' => $this->getDescription(),
      'OrderID' => $this->getOrderId(),
      'BackURL' => $this->buildReturnUrl(),
      'Timeout' => 600, // 10 minute
      'Opaque' => json_encode([
        'payment_id' => $payment->id,
        'transaction_id' => $this->getTransactionId(),
        'order_id' => $this->getOrderId(),
        'user_id' => $this->getUserId(),
        'wallet_id' => $this->getWalletId(),
      ]),
    ];

    $response = $this->http()->post($initializeUrl, $initializeData);
    $responseData = $response->getResponseContent();

    if (is_null($responseData)) {
      $payment->provider_response_message =
        'Merchant provider does not respond.';
      $payment->status = Payment::STATUS_FAILURE;
      $payment->save();

      return $this->respondError(
        'Error: ' . $payment->provider_response_message
      );
    }

    $payment->provider_transaction_id = $responseData['PaymentID'];
    $payment->provider_response_message = $responseData['ResponseMessage'];
    $payment->provider_response_code = $responseData['ResponseCode'];

    if ($responseData['ResponseCode'] != '1') {
      $payment->status = Payment::STATUS_FAILURE;
      $payment->save();

      return $this->respondError(
        $responseData['ResponseMessage'],
        $responseData['ResponseCode']
      );
    } else {
      $payment->status = Payment::STATUS_PENDING;
      $payment->save();

      $redirectUrl = $this->buildFullUrl('/VPOS/Payments/Pay', [
        'lang' => $this->languageCodes[$this->getLanguage()],
        'id' => $responseData['PaymentID'],
      ]);

      return $this->respondRedirect($redirectUrl);
    }
  }

  /**
   * Confirm payment.
   */
  public function confirm($details)
  {
    $opaqueData = json_decode($details['opaque'], true);
    $payment = $this->findPaymentById(
      $opaqueData['payment_id'],
      $opaqueData['order_id'],
      $opaqueData['transaction_id']
    );

    if (is_null($payment)) {
      return Payment::STATUS_FAILURE;
    }

    if ($payment->status == Payment::STATUS_PENDING) {
      if (
        (isset($details['resposneCode']) && '00' == $details['resposneCode']) ||
        (isset($details['responseCode']) && '00' == $details['responseCode'])
      ) {
        $payment->status = Payment::STATUS_SUCCESS;
        $this->completeTransaction($payment);
      } else {
        $payment->status = Payment::STATUS_FAILURE;
      }

      $payment->provider_description = str_replace(
        '+',
        ' ',
        $details['description']
      );
      $payment->save();
    }

    return $payment->status;
  }
}
