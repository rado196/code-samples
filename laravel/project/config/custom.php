<?php

return [
  'nikita_mobile' => [
    'username' => env('NIKITA_MOBILE_USERNAME'),
    'password' => env('NIKITA_MOBILE_PASSWORD'),
    'sender' => env('NIKITA_MOBILE_SENDER'),
  ],

  'payments' => [
    'ameriabank' => [
      'mode' => env('PAYMENT_AMERIABANK_MODE'),
      'client_id' => env('PAYMENT_AMERIABANK_CLIENT_ID'),
      'username' => env('PAYMENT_AMERIABANK_USERNAME'),
      'password' => env('PAYMENT_AMERIABANK_PASSWORD'),
    ],

    'arca' => [
      'mode' => env('PAYMENT_ARCA_MODE'),
      'username' => env('PAYMENT_ARCA_USERNAME'),
      'password' => env('PAYMENT_ARCA_PASSWORD'),
    ],

    'idram' => [
      'mode' => env('PAYMENT_IDRAM_MODE'),
      'receiver_account_id' => env('PAYMENT_IDRAM_RECEIVER_ACCOUNT_ID'),
      'secret_key' => env('PAYMENT_IDRAM_SECRET_KEY'),
    ],

    'easypay' => [
      'mode' => env('PAYMENT_EASYPAY_MODE'),
      'token' => env('PAYMENT_EASYPAY_TOKEN'),
    ],
  ],

  'web_app_url' => env('APP_WEBAPP_URL'),
  'app_admin_email' => env('APP_ADMIN_EMAIL'),
  'app_name' => env('APP_NAME'),
  'app_company_display_name' => env('APP_COMPANY_DISPLAY_NAME'),
];
