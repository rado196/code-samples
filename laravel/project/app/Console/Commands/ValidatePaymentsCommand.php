<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class ValidatePaymentsCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:validate-payments';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Validate and make unfinished payments as expired.';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $beforeExpireTime = date('Y-m-d H:i:s', strtotime('-30 minute', time()));

    Payment::query()
      ->where('created_at', '<=', $beforeExpireTime)
      ->where('status', Payment::STATUS_PENDING)
      ->update([
        'status' => Payment::STATUS_EXPIRED,
      ]);

    return Command::SUCCESS;
  }
}
