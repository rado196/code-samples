<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentsListController extends Controller
{
  private function filter(Request $request, $query)
  {
    if ($request->has('startDate')) {
      $startDate = $request->get('startDate');
      $query->whereDate('created_at', '>=', $startDate);
    }

    if ($request->has('endDate')) {
      $endDate = $request->get('endDate');
      $query
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate);
    }

    if ($request->has('provider')) {
      $provider = $request->get('provider');
      $providers = [];

      switch ($provider) {
        case 'all':
          $providers = [
            Payment::PROVIDER_AMERIA_BANK,
            Payment::PROVIDER_ARCA,
            Payment::PROVIDER_IDRAM,
            Payment::PROVIDER_EASYPAY,
          ];
          break;

        case Payment::PROVIDER_AMERIA_BANK:
          $providers = [Payment::PROVIDER_AMERIA_BANK];
          break;

        case Payment::PROVIDER_ARCA:
          $providers = [Payment::PROVIDER_ARCA];
          break;

        case Payment::PROVIDER_IDRAM:
          $providers = [Payment::PROVIDER_IDRAM];
          break;

        case Payment::PROVIDER_EASYPAY:
          $providers = [Payment::PROVIDER_EASYPAY];
          break;
      }

      $query->whereIn('provider', $providers);
    }

    return $query;
  }

  /**
   * GET: /api/admin/payments-list
   */
  public function getPayments(Request $request)
  {
    $limit = 100;
    $page = $request->get('page');

    $payments = Payment::query();
    $payments = $this->filter($request, $payments)->where('status', [
      Payment::STATUS_SUCCESS,
    ]);

    $totalCount = $payments->count();
    $sumAmount = $payments->sum('amount');
    $sumAmountWithBonus = $payments->sum('amount_with_bonus');

    $payments = $payments
      ->orderBy('created_at', 'desc')
      ->skip($limit * $page)
      ->take($limit)
      ->with('student')
      ->get();

    return response()->json([
      'payments' => $payments,
      'sumAmount' => $sumAmount,
      'sumAmountWithBonus' => $sumAmountWithBonus,
      'totalCount' => $totalCount,
      'status' => 'success',
    ]);
  }
}
