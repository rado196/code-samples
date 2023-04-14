<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Payments\ArcaPaymentProvider;
use Illuminate\Http\Request;
use App\Traits\PaymentTrait;

class ArcaController extends Controller
{
  use PaymentTrait;
  /**
   * POST: /api/payments/arca
   */
  public function initialize(Request $request)
  {
    $response = ArcaPaymentProvider::factory($request->paymentLog)
      ->setAmount($request->amount)
      ->initialize();

    return response()
      ->json($response)
      ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
  }

  /**
   * POST: /api/payments/arca/confirmation
   */
  public function confirm(Request $request)
  {
    $status = ArcaPaymentProvider::factory($request->paymentLog)->confirm(
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
