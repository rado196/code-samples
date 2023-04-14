<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('hidden_instructor_students', function (Blueprint $table) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('student_id');
      $table->unsignedBigInteger('instructor_id');
      $table->timestamps();

      $table
        ->foreign('instructor_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table
        ->foreign('student_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('hidden_instructor_students');
  }
};
