<?php

namespace App\Contracts\Payments\HttpClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Throwable;

final class PaymentHttpClient
{
  private $apiHostname = '';

  /**
   * Initiate HTTP client.
   */
  public function __construct($apiHostname)
  {
    $this->apiHostname = $this->removeSlash($apiHostname, true);
  }

  /**
   * Remove first or last slash.
   */
  private function removeSlash($string, $last)
  {
    if ($last && Str::endsWith($string, '/')) {
      return substr($string, 0, strlen($string) - 1);
    }

    if (!$last && Str::startsWith($string, '/')) {
      return substr($string, 1);
    }

    return $string;
  }

  /**
   * Build full url using endpoint and query params.
   */
  private function buildUrl($endpoint, $params)
  {
    $fullUrl = $endpoint;
    if (
      !str_starts_with($endpoint, 'http://') &&
      !str_starts_with($endpoint, 'https://')
    ) {
      $endpoint = $this->removeSlash($endpoint, false);
      $fullUrl = $this->apiHostname . '/' . $endpoint;
    }

    if (empty($params)) {
      return $fullUrl;
    }

    $glue = false !== strpos($fullUrl, '?') ? '&' : '?';
    return $fullUrl . $glue . http_build_query($params);
  }

  /**
   * Try to decode JSON data.
   */
  private function tryDecode($data)
  {
    if (is_null($data)) {
      return null;
    }

    try {
      $decoded = json_decode($data, true);
      if (is_null($decoded)) {
        return $data;
      }

      return $decoded;
    } catch (Throwable $e) {
      return $data;
    }
  }

  /**
   * Map to headers before send.
   */
  private function mapHeaders($headers)
  {
    if (
      !isset($headers['Content-Type']) &&
      !isset($headers['content-type']) &&
      !isset($headers['Content-type'])
    ) {
      $headers['Content-Type'] = 'application/json';
    }

    return $headers;
  }

  /**
   * Set multipart body.
   */
  private function multipartBody($body)
  {
    $newBody = [];
    foreach ($body as $key => $value) {
      $bodyRow = [
        'name' => $key,
        'contents' => $value,
      ];

      $newBody[] = $bodyRow;
    }

    return $newBody;
  }

  /**
   * Get content type from headers.
   */
  private function getContentType($headers)
  {
    if (isset($headers['content-type'])) {
      return $headers['content-type'];
    }
    if (isset($headers['Content-type'])) {
      return $headers['Content-type'];
    }
    if (isset($headers['Content-Type'])) {
      return $headers['Content-Type'];
    }

    return 'application/json';
  }

  /**
   * Make HTTP request.
   */
  public function request(
    $method,
    $endpoint,
    $body,
    $queryParams,
    $headers
  ): PaymentHttpCall {
    $headers = $this->mapHeaders($headers);
    $options = [
      RequestOptions::HEADERS => $headers,
    ];

    if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
      $contentType = $this->getContentType($headers);

      switch ($contentType) {
        case 'application/json':
          $options[RequestOptions::JSON] = $body;
          break;

        case 'multipart/form-data':
          unset($options[RequestOptions::HEADERS]);
          unset($headers['Content-Type']);
          unset($headers['content-type']);
          unset($headers['Content-type']);
          $options[RequestOptions::MULTIPART] = $this->multipartBody($body);
          break;
      }
    }

    $url = $this->buildUrl($endpoint, $queryParams);
    $result = new PaymentHttpCall();
    $result->setRequestUrl($url);
    $result->setRequestMethod($method);
    $result->setRequestHeaders($headers);
    $result->setRequestContent($body);

    try {
      $response = (new HttpClient())->request($method, $url, $options);
      $result->setResponseHeaders($response->getHeaders());
      $result->setResponseContent($response->getBody()->getContents());
      $result->setResponseStatusCode($response->getStatusCode());
      $result->setResponseStatusMessage($response->getReasonPhrase());
    } catch (Throwable $e) {
      if ($e instanceof RequestException || $e instanceof ClientException) {
        $response = $e->getResponse();

        $result->setResponseHeaders($response->getHeaders());
        $result->setResponseContent($response->getBody()->getContents());
        $result->setResponseStatusCode($response->getStatusCode());
        $result->setResponseStatusMessage($response->getReasonPhrase());

        $receivedHeaders = $response->getHeaders();
        $receivedBody = $this->tryDecode(
          $e
            ->getResponse()
            ->getBody()
            ->getContents()
        );
        $receivedStatusCode = $response->getStatusCode();
        $receivedStatusMessage = $response->getReasonPhrase();
        $sentUrl = $url;
        $sentMethod = $method;
        $sentHeaders = $headers;
        $sentBody = $body;

        $info = new RequestInfo(
          $receivedHeaders,
          $receivedBody,
          $receivedStatusCode,
          $receivedStatusMessage,
          $sentUrl,
          $sentMethod,
          $sentHeaders,
          $sentBody
        );

        $result->setException(PaymentHttpException::makeFrom($e, $info));
      } else {
        $result->setException($e);
      }
    }

    return $result;
  }

  /**
   * Make HTTP GET request.
   */
  public function get(
    $endpoint,
    $queryParams = [],
    $headers = []
  ): PaymentHttpCall {
    return $this->request('GET', $endpoint, [], $queryParams, $headers);
  }

  /**
   * Make HTTP POST request.
   */
  public function post(
    $endpoint,
    $body = [],
    $queryParams = [],
    $headers = []
  ): PaymentHttpCall {
    return $this->request('POST', $endpoint, $body, $queryParams, $headers);
  }

  /**
   * Make HTTP PUT request.
   */
  public function put(
    $endpoint,
    $body = [],
    $queryParams = [],
    $headers = []
  ): PaymentHttpCall {
    return $this->request('PUT', $endpoint, $body, $queryParams, $headers);
  }

  /**
   * Make HTTP DELETE request.
   */
  public function delete(
    $endpoint,
    $body = [],
    $queryParams = [],
    $headers = []
  ): PaymentHttpCall {
    return $this->request('DELETE', $endpoint, $body, $queryParams, $headers);
  }
}
