<?php

namespace App\Contracts\Payments\HttpClient;

use App\Contracts\Payments\HttpClient\RequestInfo\RequestInfoReceived;
use App\Contracts\Payments\HttpClient\RequestInfo\RequestInfoSent;

final class RequestInfo
{
  private $received = null;
  private $sent = null;

  public function __construct(
    $receivedHeaders,
    $receivedBody,
    $receivedStatusCode,
    $receivedStatusMessage,
    $sentUrl,
    $sentMethod,
    $sentHeaders,
    $sentBody
  ) {
    $this->received = new RequestInfoReceived(
      $receivedHeaders,
      $receivedBody,
      $receivedStatusCode,
      $receivedStatusMessage
    );

    $this->sent = new RequestInfoSent(
      $sentUrl,
      $sentMethod,
      $sentHeaders,
      $sentBody
    );
  }

  public function received()
  {
    return $this->received;
  }

  public function sent()
  {
    return $this->sent;
  }
}
