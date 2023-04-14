<?php

namespace App\Payments;

use App\Contracts\Payments\BasePaymentProvider;
use App\Models\Payment;

class IDramPaymentProvider extends BasePaymentProvider
{
  /** @override */
  protected function getProviderName(): string
  {
    return Payment::PROVIDER_IDRAM;
  }

  /** @override */
  protected function getReturnUrlKey(): string
  {
    return Payment::RETURN_URL_KEY_IDRAM;
  }

  /** @override */
  protected function isTestMode(): bool
  {
    $env = config('custom.payments.idram.mode');
    return $this->checkEnvIsTest($env);
  }

  /** @override */
  protected function getApiHost(): string
  {
    return 'https://banking.idram.am';
  }

  /** @override */
  protected function refund(Payment $payment)
  {
    // IDram does not support refund action.
  }

  /** @override */
  protected function complete(Payment $payment)
  {
    // IDram automatically make payments completed.
  }

  private $languageCodes = [
    Payment::LANGUAGE_ARM => 'AM',
    Payment::LANGUAGE_RUS => 'RU',
    Payment::LANGUAGE_ENG => 'EN',
  ];

  /**
   * Initialize payment.
   */
  public function initialize()
  {
    $payment = $this->initializePayment();

    $submitUrl = $this->buildFullUrl('/Payment/GetPayment');
    $submitData = [
      'EDP_LANGUAGE' => $this->languageCodes[$this->getLanguage()],
      'EDP_REC_ACCOUNT' => config('custom.payments.idram.receiver_account_id'),
      'EDP_DESCRIPTION' => $this->getDescription(),
      'EDP_AMOUNT' => $this->getAmount(),
      'EDP_BILL_NO' => $this->getOrderId(),
      'app:payment_id' => $payment->id,
      'app:transaction_id' => $this->getTransactionId(),
      'app:order_id' => $this->getOrderId(),
      'app:user_id' => $this->getUserId(),
      'app:wallet_id' => $this->getWalletId(),
    ];

    return $this->respondSubmit($submitUrl, 'POST', $submitData);
  }

  /**
   * Response to provider.
   */
  public function checking($details)
  {
    if (
      isset($details['EDP_PRECHECK']) &&
      strtoupper($details['EDP_PRECHECK']) == 'YES'
    ) {
      return $this->responseAuth($details);
    }

    return $this->responseProcess($details);
  }

  /**
   * Response to provider - auth check.
   */
  private function responseAuth($details)
  {
    $orderId = $details['EDP_BILL_NO'];
    $receiverAccountId = $details['EDP_REC_ACCOUNT'];
    $amount = $details['EDP_AMOUNT'];

    if (
      config('custom.payments.idram.receiver_account_id') != $receiverAccountId
    ) {
      $this->log('[Response auth] FAIL:ACCOUNT_ID', [
        'order_id' => $orderId,
        'receiver_account_id' => $receiverAccountId,
        'transaction_id' => $details['app:transaction_id'],
      ]);

      return 'FAIL:ACCOUNT_ID';
    }

    if ($details['app:order_id'] != $orderId) {
      $this->log('[Response auth] FAIL:INVALID_ORDER_ID', [
        'order_id' => $orderId,
        'receiver_account_id' => $receiverAccountId,
        'transaction_id' => $details['app:transaction_id'],
      ]);

      return 'FAIL:INVALID_ORDER_ID';
    }

    $payment = $this->findPaymentById(
      $details['app:payment_id'],
      $details['app:order_id'],
      $details['app:transaction_id']
    );

    if (is_null($payment)) {
      return $this->respondToAuth('FAIL:INVALID_TRANSACTION_ID', $details);
    }
    if ($payment->amount != $amount) {
      return $this->respondToAuth('FAIL:AMOUNT', $details);
    }
    if ($payment->status != Payment::STATUS_PENDING) {
      return $this->respondToAuth('FAIL:EXPIRED', $details);
    }

    // $payment->status = Payment::STATUS_SUCCESS;
    // $payment->save();

    // $this->logState('Payment successfully confirmed', $details);
    // $this->completeTransaction($payment);

    $this->updatePaymentLogUserId($payment->user_id);
    return $this->respondToAuth('OK', $details);
  }

  /**
   * Response to provider - process payment.
   */
  private function responseProcess($details)
  {
    $orderId = $details['EDP_BILL_NO'];
    $receiverAccountId = $details['EDP_REC_ACCOUNT'];
    $payerAccountId = $details['EDP_PAYER_ACCOUNT'];
    $amount = $details['EDP_AMOUNT'];
    $transactionDate = $details['EDP_TRANS_DATE'];
    $transactionId = $details['EDP_TRANS_ID'];
    $checksum = $details['EDP_CHECKSUM'];
    $hashData = implode(':', [
      config('custom.payments.idram.receiver_account_id'),
      $amount,
      config('custom.payments.idram.secret_key'),
      $orderId,
      $payerAccountId,
      $transactionId,
      $transactionDate,
    ]);

    $payment = Payment::query()
      ->where('order_id', $orderId)
      ->first();

    if (is_null($payment)) {
      return $this->respondToProcess('FAIL:INVALID_TRANSACTION_ID', $details);
    }

    $this->updatePaymentLogUserId($payment->user_id);
    if ($payment->amount != $amount) {
      return $this->respondToProcess('FAIL:AMOUNT', $details);
    }
    if ($payment->status != Payment::STATUS_PENDING) {
      return $this->respondToProcess('FAIL:EXPIRED', $details);
    }

    $hash = md5($hashData);
    if (strtoupper($checksum) != strtoupper($hash)) {
      $payment->status = Payment::STATUS_FAILURE;
      $payment->save();

      return $this->respondToProcess('FAIL:HASH', $details);
    } else {
      $payment->status = Payment::STATUS_SUCCESS;
      $payment->save();

      $this->logState('Payment successfully confirmed', $details);
      $this->completeTransaction($payment);

      return $this->respondToProcess('OK', $details);
    }
  }

  /**
   * Confirm payment.
   */
  public function confirm($details)
  {
    $payment = $this->findPaymentById(
      $details['app:payment_id'],
      $details['app:order_id'],
      $details['app:transaction_id']
    );

    if (is_null($payment)) {
      $this->logState('Payment not found!', $details);
      return Payment::STATUS_FAILURE;
    }

    if ($payment->status == Payment::STATUS_PENDING) {
      if ('success' == $details['payment_status']) {
        $payment->status = Payment::STATUS_SUCCESS;
        $payment->save();

        $this->logState('Payment successfully confirmed', $details);
        $this->completeTransaction($payment);
      } else {
        $payment->status = Payment::STATUS_FAILURE;
        $payment->save();

        $this->logState('Payment failed!', $details);
      }
    }

    return $payment->status;
  }

  private function respondToAuth($status, $details)
  {
    return $this->respondToConfirm('Response auth', $status, $details);
  }

  private function respondToProcess($status, $details)
  {
    return $this->respondToConfirm('Response process', $status, $details);
  }

  private function respondToConfirm($message, $status, $details)
  {
    $orderId = $details['EDP_BILL_NO'];
    $receiverAccountId = $details['EDP_REC_ACCOUNT'];

    $this->log('[' . $message . '] ' . $status, [
      'order_id' => $orderId,
      'receiver_account_id' => $receiverAccountId,
      'transaction_id' => $details['app:transaction_id'],
    ]);

    return $status;
  }

  private function logState($message, $details)
  {
    $debuggingData = [
      'payment_id' => $details['app:payment_id'],
      'order_id' => $details['app:order_id'],
      'transaction_id' => $details['app:transaction_id'],
    ];

    $this->log('[Confirm payment] ' . $message, $debuggingData);
  }
}
