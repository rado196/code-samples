<?php

// use Illuminate\Support\Debug\Dumper as LaravelVarDumper;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper as LaravelVarDumper;

if (!function_exists('dd')) {
  function dd(...$args)
  {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');

    foreach ($args as $x) {
      (new LaravelVarDumper())->dump($x);
    }

    die(1);
  }
}

if (!function_exists('is_console')) {
  function is_console()
  {
    if (defined('PHP_SAPI')) {
      return 'cli' == PHP_SAPI;
    }

    if (function_exists('php_sapi_name')) {
      return php_sapi_name() == 'cli';
    }

    return defined('STDIN');
  }
}

if (!function_exists('is_cli')) {
  function is_cli()
  {
    return is_console();
  }
}

if (!function_exists('generate_token')) {
  function crypto_rand_secure($min, $max)
  {
    $range = $max - $min;
    if ($range < 1) {
      return $min;
    }

    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;

    $rnd = 0;
    do {
      $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
      $rnd = $rnd & $filter;
    } while ($rnd > $range);

    return $min + $rnd;
  }

  function generate_token($length)
  {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $max = strlen($chars);

    $token = '';
    for ($i = 0; $i < $length; ++$i) {
      $token .= $chars[crypto_rand_secure(0, $max - 1)];
    }

    return $token;
  }
}

if (!function_exists('is_form_null')) {
  function is_form_null($value)
  {
    if (is_bool($value)) {
      return false;
    }

    return is_null($value) || 'null' == $value;
  }
}

if (!function_exists('notify_now')) {
  function notify_now($notifiable, Notification $notification)
  {
    try {
      $notifiableTrait = Notifiable::class;
      $traits = array_keys(class_uses($notifiable));

      if (!in_array($notifiableTrait, $traits)) {
        throw new RuntimeException(
          "Notifiable instance isn't uses ${notifiableTrait} trait."
        );
      }

      return $notifiable->notify($notification);
    } catch (Throwable $e) {
      // dd($e);
    }
  }
}

if (!function_exists('web_app_url')) {
  function web_app_url(string $endpoint)
  {
    $prefix = config('app.app_web_url');
    if ($prefix[strlen($prefix) - 1] == '/') {
      $prefix = substr($prefix . 0, strlen($prefix) - 1);
    }

    if ($endpoint[0] != '/') {
      $endpoint = '/' . $endpoint;
    }

    return $prefix . $endpoint;
  }
}

if (!function_exists('unique_code')) {
  function unique_code($limit)
  {
    $uniqueCode = substr(
      base_convert(sha1(uniqid(mt_rand())), 16, 36),
      0,
      $limit
    );
    $uuid = Str::uuid();

    return $uniqueCode . ':' . $uuid;
  }
}
