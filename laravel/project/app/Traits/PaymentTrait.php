<?php

namespace App\Traits;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentTheoreticalPartTraining;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingPrice;
// use App\Models\StudentAppointment;

trait PaymentTrait
{
  private function validateUnpaid()
  {
    $authId = Auth::id();

    $wallet = Wallet::query()
      ->where('user_id', $authId)
      ->first();

    // $studentAppointments = StudentAppointment::query()
    //   ->where('student_id', $authId)
    //   ->where('status', StudentAppointment::STATUS_PENDING)
    //   ->get();

    // foreach ($studentAppointments as $studentAppointment) {
    //   if ($wallet->balance >= $studentAppointment->price) {
    //     $wallet->decrement('balance', $studentAppointment->price);

    //     $studentAppointment->status = StudentAppointment::STATUS_BOOKED;
    //     $studentAppointment->save();
    //   }
    // }

    $studentTheoreticalPartTraining = StudentTheoreticalPartTraining::query()
      ->where('student_id', $authId)
      ->where('status', StudentTheoreticalPartTraining::STATUS_PENDING)
      ->first();

    if ($studentTheoreticalPartTraining) {
      $theoreticalPartTrainingPrice = TheoreticalPartTrainingPrice::first();

      if ($wallet->balance >= $theoreticalPartTrainingPrice->price) {
        $wallet->decrement('balance', $theoreticalPartTrainingPrice->price);

        $studentTheoreticalPartTraining->status =
          StudentTheoreticalPartTraining::STATUS_PAID;
        $studentTheoreticalPartTraining->save();
      }
    }
  }
}
