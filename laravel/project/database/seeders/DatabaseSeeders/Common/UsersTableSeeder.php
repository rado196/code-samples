<?php

namespace Database\Seeders\DatabaseSeeders\Common;

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
      'phone' => $userData['phone'],
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
    // admin
    $this->create([
      'first_name' => 'Admin',
      'last_name' => 'Admin',
      'email' => 'admin@admin.com',
      'phone' => '000000000',
      'password' => 'admin2022!',
      'role' => User::ROLE_ADMIN,
      'gender' => User::GENDER_FEMALE,
    ]);
  }
}
