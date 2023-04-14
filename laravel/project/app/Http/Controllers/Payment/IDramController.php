<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Payments\IDramPaymentProvider;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class IDramController extends Controller
{
  use PaymentTrait;
  /**
   * POST: /api/payments/idram
   */
  public function initialize(Request $request)
  {
    $response = IDramPaymentProvider::factory($request->paymentLog)
      ->setAmount($request->amount)
      ->initialize();

    return response()
      ->json($response)
      ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }

  /**
   * POST: /api/payments/idram/checking
   */
  public function checking(Request $request)
  {
    $status = IDramPaymentProvider::factory($request->paymentLog)->checking(
      $request->post()
    );

    return response($status);
  }

  /**
   * POST: /api/payments/idram/confirmation
   */
  public function confirm(Request $request)
  {
    $status = IDramPaymentProvider::factory($request->paymentLog)->confirm(
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
