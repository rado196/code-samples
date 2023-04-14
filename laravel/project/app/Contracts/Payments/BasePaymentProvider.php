<?php

namespace App\Contracts\Payments;

use App\Contracts\Payments\HttpClient\PaymentHttpClient;
use App\Contracts\Payments\Responses\InitializationResponse;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\LogErrorTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class BasePaymentProvider
{
  use LogErrorTrait;

  /**
   * @return static
   */
  public static function factory(PaymentLog $log)
  {
    $paymentProvider = new static();
    $paymentProvider->paymentLog = $log;

    return $paymentProvider;
  }

  abstract protected function isTestMode(): bool;
  abstract protected function getProviderName(): string;
  abstract protected function getReturnUrlKey(): string;
  abstract protected function getApiHost(): string;
  abstract protected function refund(Payment $payment);
  abstract protected function complete(Payment $payment);

  final protected function updatePaymentLogUserId($userId)
  {
    if (!is_null($this->paymentLog)) {
      $this->paymentLog->user_id = $userId;
      $this->paymentLog->save();
    }
  }

  protected function getUser()
  {
    $user = Auth::user();
    if (!is_null($user)) {
      $this->updatePaymentLogUserId($user->id);
    }

    return $user;
  }

  final protected function log($message, $data = [])
  {
    $this->paymentDebugging(
      '[' . $this->getProviderName() . ' provider]',
      $message,
      $data
    );
  }

  /**
   * Build return url.
   */
  final protected function checkEnvIsTest($env)
  {
    return in_array($env, ['dev', 'development', 'test', 'testing']);
  }

  /**
   * Build return url.
   */
  final protected function buildReturnUrl($queryParams = [])
  {
    $url = config('app.app_web_url') . '/payments/' . $this->getReturnUrlKey();
    foreach ($queryParams as $key => $value) {
      $glue = strpos($url, '?') === false ? '?' : '&';
      $url .= $glue . $key . '=' . $value;
    }

    return $url;
  }

  /**
   * Build success return url.
   */
  final protected function buildReturnSuccessUrl()
  {
    return $this->buildReturnUrl(['payment_status' => 'success']);
  }

  /**
   * Build failure return url.
   */
  final protected function buildReturnFailureUrl()
  {
    return $this->buildReturnUrl(['payment_status' => 'failure']);
  }

  /**
   * Get amount with bonus.
   */
  protected function getAmountWithBonus($amount)
  {
    // e.g. if we want to add +20% bonus to wallet, then
    // we need to return $amount * 1.2
    return $amount;
  }

  /**
   * Create http requester instance.
   */
  final protected function http()
  {
    return new PaymentHttpClient($this->getApiHost());
  }

  /**
   * Build final url with endpoint and query string.
   */
  final protected function buildUrl($url, $queryParams = [])
  {
    foreach ($queryParams as $key => $value) {
      $glue = false !== strpos($url, '?') ? '&' : '?';
      $url .= $glue . $key . '=' . $value;
    }

    return $url;
  }

  /**
   * Build full url with hostname, endpoint and query string.
   */
  final protected function buildFullUrl($url, $queryParams = [])
  {
    $hostname = $this->getApiHost();
    $endpoint = $this->buildUrl($url, $queryParams);

    return $hostname . $endpoint;
  }

  /**
   * Build order id for transaction.
   */
  private function buildOrderId(User $user)
  {
    $paymentsHistoryCount = Payment::query()
      ->where('user_id', $user->id)
      ->count();

    $idPrefix = $user->id * 10000;

    return $idPrefix + $paymentsHistoryCount;
  }

  /**
   * Build payment description.
   */
  private function buildDescription(User $user)
  {
    $fullName = $user->first_name . ' ' . $user->last_name;
    $prefix = 'Recharge Account Balance';

    $keyOrderId = 'OID:' . $this->orderId;
    $keyUserId = 'UID:' . $user->id;

    return "$prefix - $fullName ($keyUserId, $keyOrderId).";
  }

  /**
   * Get wallet id for user.
   */
  private function getWalletIdForUser(User $user)
  {
    $wallet = Wallet::query()
      ->where('user_id', $user->id)
      ->first();

    return $wallet->id;
  }

  private $paymentLog = null;
  private $provider = null;
  private $userId = null;
  private $walletId = null;
  private $amount = null;
  private $amount_with_bonus = null;
  private $bonus = null;
  private $status = null;
  private $currency = null;
  private $description = null;
  private $language = null;
  private $transactionId = null;
  private $providerTransactionId = null;
  private $orderId = null;

  /**
   * Get payment provider.
   */
  final public function getProvider()
  {
    return $this->provider;
  }

  /**
   * Get user id.
   */
  final public function getUserId()
  {
    return $this->userId;
  }

  /**
   * Get wallet id.
   */
  final public function getWalletId()
  {
    return $this->walletId;
  }

  /**
   * Get billable amount.
   */
  final public function getAmount()
  {
    return $this->amount;
  }

  /**
   * Set billable amount.
   */
  final public function setAmount($amount)
  {
    $this->amount = $amount;
    $this->amount_with_bonus = $this->getAmountWithBonus($amount);
    $this->bonus = $this->amount_with_bonus - $this->amount;

    return $this;
  }

  /**
   * Set payment description.
   */
  final public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * Set payment currency.
   */
  final public function setCurrency($currency)
  {
    $this->currency = $currency;
    return $this;
  }

  /**
   * Get payment currency.
   */
  final public function getCurrency()
  {
    return $this->currency;
  }

  /**
   * Get payment description.
   */
  final public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set interface language.
   */
  final public function setLanguage($language)
  {
    $this->language = $language;
    return $this;
  }

  /**
   * Get interface language.
   */
  final public function getLanguage()
  {
    return $this->language;
  }

  /**
   * Get transaction id.
   */
  final public function getTransactionId()
  {
    return $this->transactionId;
  }

  /**
   * Set provider transaction id.
   */
  final public function setProviderTransactionId($providerTransactionId)
  {
    $this->providerTransactionId = $providerTransactionId;
    return $this;
  }

  /**
   * Get order id.
   */
  final public function getOrderId()
  {
    return $this->orderId;
  }

  /**
   * Initialize payment instance.
   */
  final protected function initializePayment(): Payment
  {
    $user = $this->getUser();

    $this->provider = $this->getProviderName();
    $this->transactionId = Str::uuid();
    $this->currency = Payment::CURRENCY_AMD;
    $this->status = Payment::STATUS_PENDING;
    $this->language = Payment::LANGUAGE_ARM;

    if (!is_null($user)) {
      $this->userId = $user->id;
      $this->orderId = $this->buildOrderId($user);
      $this->description = $this->buildDescription($user);
      $this->walletId = $this->getWalletIdForUser($user);
    }

    return Payment::create([
      'provider' => $this->provider,
      'user_id' => $this->userId,
      'wallet_id' => $this->walletId,
      'amount' => $this->amount,
      'amount_with_bonus' => $this->amount_with_bonus,
      'currency' => $this->currency,
      'bonus' => $this->bonus,
      'status' => $this->status,
      'description' => $this->description,
      'order_id' => $this->orderId,
      'transaction_id' => $this->transactionId,
      'provider_transaction_id' => $this->providerTransactionId,
    ]);
  }

  /**
   * Complete transaction and update wallet balance
   */
  final protected function completeTransaction(Payment $payment): bool
  {
    $this->complete($payment);

    $wallet = Wallet::find($payment->wallet_id);
    if (!is_null($wallet)) {
      $wallet->balance += $payment->amount_with_bonus;
      $wallet->save();

      return true;
    }

    $this->refund($payment);

    $payment->status = Payment::STATUS_REFUNDED;
    $payment->save();

    return false;
  }

  /**
   * Get payment model instance by id.
   */
  final protected function findPaymentById($paymentId, $orderId, $transactionId)
  {
    return Payment::query()
      ->where('order_id', $orderId)
      ->where('transaction_id', $transactionId)
      ->where('id', $paymentId)
      ->first();
  }

  /**
   * Get payment model instance by id.
   */
  final protected function findPaymentByProviderId($paymentProviderId)
  {
    return Payment::query()
      ->where('provider_transaction_id', $paymentProviderId)
      ->first();
  }

  /**
   * Respond error to client.
   */
  final protected function respondError($message = 'unknown', $code = 1)
  {
    return InitializationResponse::factory()
      ->setStatusError()
      ->setErrorMessage($message)
      ->setErrorCode($code)
      ->build();
  }

  /**
   * Respond redirect to client.
   */
  final protected function respondRedirect($url)
  {
    return InitializationResponse::factory()
      ->setStatusRedirect()
      ->setRedirectUrl($url)
      ->build();
  }

  /**
   * Respond form-submit to client.
   */
  final protected function respondSubmit($url, $method, $data)
  {
    return InitializationResponse::factory()
      ->setStatusFormSubmit()
      ->setFormSubmitMethod($method)
      ->setFormSubmitUrl($url)
      ->setFormSubmitData($data)
      ->build();
  }
}
