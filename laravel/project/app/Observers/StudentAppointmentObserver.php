<?php

namespace App\Observers;

use App\Contracts\Models\BaseModelObserver;
use App\Mail\AppointmentCreation;
use App\Models\StudentAppointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class StudentAppointmentObserver extends BaseModelObserver
{
  protected static function model()
  {
    return StudentAppointment::class;
  }

  /**
   * Handle the StudentAppointment "created" event.
   *
   * @param  \App\Models\StudentAppointment  $studentAppointment
   * @return void
   */
  public function created(StudentAppointment $studentAppointment)
  {
    //
  }

  /**
   * Handle the StudentAppointment "updated" event.
   *
   * @param  \App\Models\StudentAppointment  $studentAppointment
   * @return void
   */
  public function updated(StudentAppointment $studentAppointment)
  {
    if ($studentAppointment->isDirty('status')) {
      if ($studentAppointment->status === StudentAppointment::STATUS_BOOKED) {
        $nowDate = Carbon::now();
        $nowFormattedDateTime = $nowDate->format('Y-m-d H');
        $nowFormattedDateTime = Carbon::createFromFormat(
          'Y-m-d H',
          $nowFormattedDateTime
        );

        $aptDateTime =
          $studentAppointment->date . ' ' . $studentAppointment->start_time;
        $formattedAptDateTime = Carbon::createFromFormat(
          'Y-m-d H',
          date('Y-m-d H', strtotime($aptDateTime))
        );

        $diffInHours = $formattedAptDateTime->diffInHours(
          $nowFormattedDateTime
        );

        if ($diffInHours <= 24) {
          $mail = new AppointmentCreation(
            $studentAppointment->student->first_name .
              ' ' .
              $studentAppointment->student->last_name,
            $studentAppointment->instructor->first_name .
              ' ' .
              $studentAppointment->instructor->last_name,
            $studentAppointment->date,
            $studentAppointment->start_time,
            $studentAppointment->end_time
          );

          Mail::to(config('custom.app_admin_email'))->send($mail);
        }
      }
    }
  }

  /**
   * Handle the StudentAppointment "deleted" event.
   *
   * @param  \App\Models\StudentAppointment  $studentAppointment
   * @return void
   */
  public function deleted(StudentAppointment $studentAppointment)
  {
    //
  }

  /**
   * Handle the StudentAppointment "restored" event.
   *
   * @param  \App\Models\StudentAppointment  $studentAppointment
   * @return void
   */
  public function restored(StudentAppointment $studentAppointment)
  {
    //
  }

  /**
   * Handle the StudentAppointment "force deleted" event.
   *
   * @param  \App\Models\StudentAppointment  $studentAppointment
   * @return void
   */
  public function forceDeleted(StudentAppointment $studentAppointment)
  {
    //
  }
}
