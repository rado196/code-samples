<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Payments\EasyPayPaymentProvider;
use Illuminate\Http\Request;

class EasyPayController extends Controller
{
  /**
   * POST: /api/payments/easy-pay/check
   */
  public function check(Request $request)
  {
    $data = $request->all();

    $response = EasyPayPaymentProvider::factory($request->paymentLog)->checking(
      $data
    );

    return response()
      ->json($response)
      ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }

  /**
   * POST: /api/payments/easy-pay/payment
   */
  public function payment(Request $request)
  {
    $data = $request->all();

    $response = EasyPayPaymentProvider::factory($request->paymentLog)
      ->setAmount($data['Amount'])
      ->initialize($data);

    return response()
      ->json($response)
      ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }
}
