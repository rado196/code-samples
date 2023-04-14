<?php

namespace App\Contracts\Payments\HttpClient\RequestInfo;

final class RequestInfoSent
{
  private $url = '';
  private $method = '';
  private $headers = [];
  private $body = '';

  /**
   * Initiate request information.
   */
  public function __construct($url, $method, $headers, $body)
  {
    $this->url = $url;
    $this->method = $method;
    $this->headers = $headers;
    $this->body = $body;
  }

  /**
   * Get request url.
   */
  public function url()
  {
    return $this->url;
  }

  /**
   * Get request method.
   */
  public function method()
  {
    return $this->method;
  }

  /**
   * Get request headers.
   */
  public function headers()
  {
    return $this->headers;
  }

  /**
   * Get request body.
   */
  public function body()
  {
    return $this->body;
  }
}
