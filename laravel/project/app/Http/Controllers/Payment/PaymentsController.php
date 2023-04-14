<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentsController extends Controller
{
  public function getHistories(Request $request)
  {
    $authId = Auth::id();

    $histories = Payment::query()
      ->where('user_id', $authId)
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json([
      'histories' => $histories,
      'status' => 'success',
    ]);
  }
}
