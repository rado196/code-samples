<?php

namespace Database\Seeders\DatabaseSeeders\Development;

use App\Models\User;
use Database\Factories\UserStudentFactory;
use Database\Factories\UserTeacherFactory;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    UserTeacherFactory::new()
      ->count(3)
      ->create();

    UserStudentFactory::new()
      ->count(50)
      ->create();
  }
}
