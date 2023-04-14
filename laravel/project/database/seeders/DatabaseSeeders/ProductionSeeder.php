<?php

namespace Database\Seeders\DatabaseSeeders;

use Database\Seeders\DatabaseSeeders\Production\UsersTableSeeder;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->call(UsersTableSeeder::class);
  }
}
