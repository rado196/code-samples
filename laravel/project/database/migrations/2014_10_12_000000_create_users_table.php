<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  private $roles = [
    User::ROLE_ADMIN,
    User::ROLE_STUDENT,
    User::ROLE_INSTRUCTOR,
  ];

  private $providers = [
    User::PROVIDER_STANDARD,
    User::PROVIDER_GOOGLE,
    User::PROVIDER_FACEBOOK,
    User::PROVIDER_APPLE,
  ];

  private $genders = [User::GENDER_MALE, User::GENDER_FEMALE];

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id()->startingValue(625067);
      $table->string('avatar');
      $table->enum('provider', $this->providers);
      $table->enum('role', $this->roles);
      $table->string('first_name');
      $table->string('last_name');
      $table->enum('gender', $this->genders);
      $table->string('email')->unique();
      $table
        ->string('phone')
        ->unique()
        ->nullable();
      $table->timestamp('verified_at')->nullable();
      $table->string('password')->nullable();
      $table->string('apple_id')->nullable();
      $table->string('facebook_id')->nullable();
      $table->string('google_id')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('users');
  }
};
