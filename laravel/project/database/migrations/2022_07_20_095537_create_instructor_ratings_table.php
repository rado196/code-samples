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
    Schema::create('instructor_ratings', function (Blueprint $table) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('instructor_id');
      $table->unsignedBigInteger('student_id');
      $table->integer('rating');
      $table->text('comment')->nullable();
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
    Schema::dropIfExists('instructor_ratings');
  }
};
