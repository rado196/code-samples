<?php

namespace App\Console\Commands;

use App\Models\StudentTheoreticalPartTraining;
use Illuminate\Console\Command;

class ValidateStudentTheoreticalPartTrainingCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:validate-student-theoretical-part-training';

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

    StudentTheoreticalPartTraining::query()
      ->where('created_at', '<=', $beforeExpireTime)
      ->where('status', StudentTheoreticalPartTraining::STATUS_PENDING)
      ->update([
        'status' => StudentTheoreticalPartTraining::STATUS_EXPIRED,
      ]);

    return Command::SUCCESS;
  }
}
