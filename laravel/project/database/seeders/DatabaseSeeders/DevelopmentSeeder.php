<?php

namespace Database\Seeders\DatabaseSeeders;

use Database\Seeders\DatabaseSeeders\Development\ExamGroupSeeder;
use Database\Seeders\DatabaseSeeders\Development\UsersTableSeeder;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->call(UsersTableSeeder::class);
    $this->call(ExamGroupSeeder::class);
  }
}
