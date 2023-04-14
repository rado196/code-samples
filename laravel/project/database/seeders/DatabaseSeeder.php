<?php

namespace Database\Seeders;

use Database\Seeders\DatabaseSeeders\CommonSeeder;
use Database\Seeders\DatabaseSeeders\DevelopmentSeeder;
use Database\Seeders\DatabaseSeeders\ProductionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(CommonSeeder::class);

    if ('local' == config('app.env')) {
      $this->call(DevelopmentSeeder::class);
    } else {
      $this->call(ProductionSeeder::class);
    }
  }
}
