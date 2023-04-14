<?php

namespace Database\Seeders\DatabaseSeeders\Production;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
  private function create(array $userData)
  {
    return User::create([
      'provider' => User::PROVIDER_STANDARD,
      'verified_at' => '2022-01-01 00:00:00',
      'role' => $userData['role'],
      'first_name' => $userData['first_name'],
      'last_name' => $userData['last_name'],
      'email' => $userData['email'],
      'gender' => $userData['gender'],
      'password' => Hash::make($userData['password']),
    ]);
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
  }
}
