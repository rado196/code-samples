<?php

namespace App\Traits;

trait LogErrorTrait
{
  private function paymentDebugging($type, $message, $data)
  {
    $path = storage_path('logs/payments/' . date('Ymd') . '.log');
    $folderPath = dirname($path);

    if (!file_exists($folderPath)) {
      mkdir($folderPath, 0666);
    }

    file_put_contents(
      $path,
      '-----------------------  ' .
        date('Y-m-d H:i:s') .
        ' -----------------------' .
        PHP_EOL,
      FILE_APPEND
    );
    file_put_contents($path, 'type' . ' -> ' . $type . PHP_EOL, FILE_APPEND);
    file_put_contents(
      $path,
      'message' . ' -> ' . $message . PHP_EOL,
      FILE_APPEND
    );

    if (count($data) > 0) {
      foreach ($data as $key => $item) {
        file_put_contents($path, $key . ' -> ' . $item . PHP_EOL, FILE_APPEND);
      }
    }

    file_put_contents($path, PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
  }
}
