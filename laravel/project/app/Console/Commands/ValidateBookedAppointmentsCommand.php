<?php

namespace App\Console\Commands;

use App\Models\StudentAppointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ValidateBookedAppointmentsCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:validate-booked-appointments';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'If current time  >= appointment time, status change in completed!';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $nowDate = Carbon::now();
    $nowFormattedDateTime = $nowDate->format('Y-m-d H:i:s');
    $updatedAppointments = [];

    $appointments = StudentAppointment::query()
      ->where('status', StudentAppointment::STATUS_BOOKED)
      ->get();

    foreach ($appointments as $appointment) {
      $aptDateTime = $appointment->date . ' ' . $appointment->end_time;

      if ($nowFormattedDateTime >= $aptDateTime) {
        $updatedAppointments[] = $appointment->id;
      }
    }

    StudentAppointment::query()
      ->whereIn('id', $updatedAppointments)
      ->update([
        'status' => StudentAppointment::STATUS_COMPLETED,
      ]);

    return Command::SUCCESS;
  }
}
