<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\StudentAppointment;
use App\Notifications\AppointmentReminderBefore24Notification;
use App\Notifications\AppointmentReminderBefore3Notification;

class AppointmentNotificationCommand extends Command
{
  const BEFORE_24_HOURS = 24;
  const BEFORE_3_HOURS = 3;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:appointment-notification';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send appointment notification';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $nowDate = Carbon::now();
    $nowFormattedDateTime = $nowDate->format('Y-m-d H');
    $nowFormattedDateTime = Carbon::createFromFormat(
      'Y-m-d H',
      $nowFormattedDateTime
    );

    $appointments = StudentAppointment::query()
      ->where('status', StudentAppointment::STATUS_BOOKED)
      ->get();

    foreach ($appointments as $appointment) {
      $aptDateTime = $appointment->date . ' ' . $appointment->start_time;
      $formattedAptDateTime = Carbon::createFromFormat(
        'Y-m-d H',
        date('Y-m-d H', strtotime($aptDateTime))
      );

      $diffInHours = $formattedAptDateTime->diffInHours($nowFormattedDateTime);

      switch ($diffInHours) {
        case self::BEFORE_24_HOURS:
          $notification = new AppointmentReminderBefore24Notification(
            $appointment->date,
            $appointment->start_time,
            $appointment->instructor->first_name .
              ' ' .
              $appointment->instructor->last_name
          );

          $appointment->student->notify($notification);
          break;

        case self::BEFORE_3_HOURS:
          $notification = new AppointmentReminderBefore3Notification(
            $appointment->date,
            $appointment->start_time,
            $appointment->instructor->first_name .
              ' ' .
              $appointment->instructor->last_name
          );

          $appointment->student->notify($notification);
          break;
      }
    }

    return Command::SUCCESS;
  }
}
