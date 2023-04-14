<?php

namespace App\Console\Commands;

use App\Models\StudentTheoreticalPartTraining;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ValidateExpiredStudentTheoreticalPartTrainingCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:validate-expired-student-theoretical-part-training';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Set expiration date if created_at <= current date - 30';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $beforeExpireTime = date(
      'Y-m-d',
      strtotime(
        '-' . StudentTheoreticalPartTraining::EXPIRATION_DATE . ' days',
        time()
      )
    );

    StudentTheoreticalPartTraining::query()
      ->where('created_at', '<=', $beforeExpireTime)
      ->whereNull('expiration_date')
      ->update([
        'expiration_date' => Carbon::now(),
      ]);

    return Command::SUCCESS;
  }
}
