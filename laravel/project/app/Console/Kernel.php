<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule->command('app:validate-appointments')->everyTwoMinutes();
    $schedule->command('app:validate-payments')->everyTwoMinutes();
    $schedule->command('app:validate-booked-appointments')->everyTwoMinutes();
    $schedule->command('app:appointment-notification')->hourly();

    $schedule
      ->command('app:validate-student-theoretical-part-training')
      ->everyTwoMinutes();
    $schedule
      ->command('app:validate-expired-student-theoretical-part-training')
      ->everyTwoMinutes();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
