<?php

namespace Database\Seeders\DatabaseSeeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class GenerateApiKeySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $appKey = config('app.key');
    if (is_null($appKey) || empty($appKey)) {
      Artisan::call('key:generate');
    }
  }
}
