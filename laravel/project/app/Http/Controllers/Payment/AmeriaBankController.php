<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Payments\AmeriaBankPaymentProvider;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class AmeriaBankController extends Controller
{
  use PaymentTrait;

  /**
   * POST: /api/payments/ameriabank
   */
  public function initialize(Request $request)
  {
    $response = AmeriaBankPaymentProvider::factory($request->paymentLog)
      ->setAmount($request->amount)
      ->initialize();

    return response()
      ->json($response)
      ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }

  /**
   * POST: /api/payments/ameriabank/confirmation
   */
  public function confirm(Request $request)
  {
    $status = AmeriaBankPaymentProvider::factory($request->paymentLog)->confirm(
      $request->paymentDetails
    );

    if ('success' === $status) {
      $this->validateUnpaid();
    }

    return response()->json([
      'status' => $status,
    ]);
  }
}
