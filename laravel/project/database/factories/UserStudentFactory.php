<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserStudentFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = User::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'first_name' => $this->faker->firstName(),
      'last_name' => $this->faker->lastName(),
      'gender' => $this->faker->randomElement([
        User::GENDER_MALE,
        User::GENDER_FEMALE,
      ]),
      'role' => User::ROLE_STUDENT,
      'email' => $this->faker->unique()->safeEmail(),
      'phone' => $this->faker->unique()->phoneNumber(),
      'verified_at' => '2022-01-01 00:00:00',
      'password' => Hash::make('password'),
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   *
   * @return static
   */
  public function unverified()
  {
    return $this->state(function (array $attributes) {
      return [];
    });
  }
}
