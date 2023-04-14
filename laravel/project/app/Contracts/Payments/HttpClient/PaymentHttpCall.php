<?php

namespace App\Contracts\Payments\HttpClient;

use Throwable;

class PaymentHttpCall
{
  private $requestUrl = null;
  private $requestMethod = null;
  private $requestHeaders = null;
  private $requestContent = null;
  private $responseHeaders = null;
  private $responseContent = null;
  private $responseStatusCode = null;
  private $responseStatusMessage = null;
  private $exception = null;

  private function tryJsonParse($value)
  {
    try {
      return json_decode($value, true);
    } catch (Throwable $e) {
      return $value;
    }
  }

  /**
   * Set request URL.
   */
  public function setRequestUrl($requestUrl)
  {
    $this->requestUrl = $requestUrl;
    return $this;
  }

  /**
   * Get request URL.
   */
  public function getRequestUrl()
  {
    return $this->requestUrl;
  }

  /**
   * Set request method.
   */
  public function setRequestMethod($requestMethod)
  {
    $this->requestMethod = $requestMethod;
    return $this;
  }

  /**
   * Get request method.
   */
  public function getRequestMethod()
  {
    return $this->requestMethod;
  }

  /**
   * Set request headers.
   */
  public function setRequestHeaders($requestHeaders)
  {
    $this->requestHeaders = $this->tryJsonParse($requestHeaders);
    return $this;
  }

  /**
   * Get request headers.
   */
  public function getRequestHeaders()
  {
    return $this->requestHeaders;
  }

  /**
   * Set request body.
   */
  public function setRequestContent($requestContent)
  {
    $this->requestContent = $this->tryJsonParse($requestContent);
    return $this;
  }

  /**
   * Get request body.
   */
  public function getRequestContent()
  {
    return $this->requestContent;
  }

  /**
   * Set response headers.
   */
  public function setResponseHeaders($responseHeaders)
  {
    $this->responseHeaders = $this->tryJsonParse($responseHeaders);
    return $this;
  }

  /**
   * Get response headers.
   */
  public function getResponseHeaders()
  {
    return $this->responseHeaders;
  }

  /**
   * Set response body.
   */
  public function setResponseContent($responseContent)
  {
    $this->responseContent = $this->tryJsonParse($responseContent);
    return $this;
  }

  /**
   * Get response body.
   */
  public function getResponseContent()
  {
    return $this->responseContent;
  }

  /**
   * Set response status code.
   */
  public function setResponseStatusCode($responseStatusCode)
  {
    $this->responseStatusCode = $responseStatusCode;
    return $this;
  }

  /**
   * Get response status code.
   */
  public function getResponseStatusCode()
  {
    return $this->responseStatusCode;
  }

  /**
   * Set response status message.
   */
  public function setResponseStatusMessage($responseStatusMessage)
  {
    $this->responseStatusMessage = $responseStatusMessage;
    return $this;
  }

  /**
   * Get response status message.
   */
  public function getResponseStatusMessage()
  {
    return $this->responseStatusMessage;
  }

  /**
   * Set exception instance.
   */
  public function setException($exception)
  {
    $this->exception = $exception;
    return $this;
  }

  /**
   * Get exception instance.
   */
  public function getException()
  {
    return $this->exception;
  }
}
