<?php

namespace Database\Seeders\DatabaseSeeders;

use Database\Seeders\DatabaseSeeders\Common\QuestionCategoriesSeeder;
use Database\Seeders\DatabaseSeeders\Common\GenerateApiKeySeeder;
use Database\Seeders\DatabaseSeeders\Common\LanguagesSeeder;
use Database\Seeders\DatabaseSeeders\Common\LinkStorageSeeder;
use Database\Seeders\DatabaseSeeders\Common\TheoreticalPartLessonsSeeder;
use Database\Seeders\DatabaseSeeders\Common\UsersTableSeeder;
use Illuminate\Database\Seeder;

class CommonSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->call(GenerateApiKeySeeder::class);
    $this->call(LinkStorageSeeder::class);
    $this->call(UsersTableSeeder::class);
    $this->call(LanguagesSeeder::class);
    $this->call(QuestionCategoriesSeeder::class);
    $this->call(TheoreticalPartLessonsSeeder::class);
  }
}
