<?php

namespace App\Console\Commands;

use App\Models\StudentAppointment;
use Illuminate\Console\Command;

class ValidateAppointmentsCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:validate-appointments';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Validate and make unpaid appointments as expired.';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $beforeExpireTime = date('Y-m-d H:i:s', strtotime('-30 minute', time()));

    StudentAppointment::query()
      ->where('created_at', '<=', $beforeExpireTime)
      ->where('status', StudentAppointment::STATUS_PENDING)
      ->update([
        'status' => StudentAppointment::STATUS_EXPIRED,
      ]);

    return Command::SUCCESS;
  }
}
