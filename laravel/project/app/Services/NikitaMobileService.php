<?php

namespace App\Services;

use App\Contracts\Services\BaseService;
use GuzzleHttp\Client as GuzzleHttpClient;

class NikitaMobileService extends BaseService
{
  private function execute($body)
  {
    $authData = [
      config('custom.nikita_mobile.username'),
      config('custom.nikita_mobile.password'),
    ];

    $httpClient = new GuzzleHttpClient([
      'base_uri' => 'http://45.131.124.7',
      'timeout' => 10_000,
      'auth' => $authData,
      'headers' => [
        'Authorization' => [
          'Basic ' . base64_encode($authData[0] . ':' . $authData[1]),
        ],
        'Content-Type' => ['application/json; charset=utf-8'],
      ],
    ]);

    $options = [
      'auth' => $authData,
      'body' => json_encode($body),
      'allow_redirects' => false,
    ];

    return $httpClient->post('/broker-api/send', $options)->getBody();
  }

  private function buildMessageId()
  {
    return date('Ymdihs');
  }

  public function sendSms($phoneNumber, $message)
  {
    // priorities
    // 2 - low
    // 4 - normal
    // 6 - high
    // 8 - realtime

    $this->execute([
      'messages' => [
        [
          'message-id' => $this->buildMessageId(),
          'recipient' => $phoneNumber,
          'priority' => '6',
          'sms' => [
            'originator' => config('custom.nikita_mobile.sender'),
            'content' => [
              'text' => $message,
            ],
          ],
        ],
      ],
    ]);
  }
}
