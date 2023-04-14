<?php

namespace App\Contracts\Payments\HttpClient\RequestInfo;

final class RequestInfoReceived
{
  private function parseHeaders()
  {
    foreach ($this->headers as $key => $value) {
      if (is_array($value) && count($value) === 1) {
        $this->headers[$key] = $value[0];
      }
    }
  }

  private $headers = [];
  private $body = '';
  private $statusCode = 0;
  private $statusMessage = '';

  /**
   * Initiate response information.
   */
  public function __construct($headers, $body, $statusCode, $statusMessage)
  {
    $this->headers = $headers;
    $this->body = $body;
    $this->statusCode = $statusCode;
    $this->statusMessage = $statusMessage;

    $this->parseHeaders();
  }

  /**
   * Get response headers.
   */
  public function headers()
  {
    return $this->headers;
  }

  /**
   * Get response body.
   */
  public function body()
  {
    return $this->body;
  }

  /**
   * Get response status code.
   */
  public function statusCode()
  {
    return $this->statusCode;
  }

  /**
   * Get response status message.
   */
  public function statusMessage()
  {
    return $this->statusMessage;
  }
}
