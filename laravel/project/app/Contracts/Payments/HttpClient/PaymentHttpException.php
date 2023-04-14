<?php

namespace App\Contracts\Payments\HttpClient;

use Exception;
use Throwable;

final class PaymentHttpException extends Exception
{
  /**
   *
   */
  public static function makeFrom(Throwable $httpException, $info)
  {
    $message =
      'Payment HTTP exception was occurred: ' . $httpException->getMessage();
    return new PaymentHttpException($message, $httpException, $info);
  }

  private $httpException = null;
  private $information = null;

  /**
   * Initiate exception.
   */
  public function __construct(
    $message,
    Throwable $httpException,
    RequestInfo $information
  ) {
    parent::__construct($message);

    $this->httpException = $httpException;
    $this->information = $information;
  }

  /**
   * GetHTTP exception.
   * @return Throwable
   */
  public function getHttpException()
  {
    return $this->httpException;
  }

  /**
   * Get request information.
   * @return RequestInfo
   */
  public function getInformation()
  {
    return $this->information;
  }

  /**
   * Exception to string.
   * @return string
   */
  public function __toString(): string
  {
    return json_encode([
      'stackTrace' => $this->getTraceAsString(),
      'message' => $this->getMessage(),
    ]);
  }
}
