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
    Schema::create('user_exam_tests', function (Blueprint $table) {
      $table->id()->startingValue(3451200541);
      $table->unsignedBigInteger('user_id')->nullable();
      $table->unsignedBigInteger('exam_test_id');
      $table->string('unique_id')->unique();
      $table->boolean('is_completed')->default(false);
      $table->integer('finish_time')->nullable();
      $table->timestamps();

      $table
        ->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table
        ->foreign('exam_test_id')
        ->references('id')
        ->on('exam_tests')
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
    Schema::dropIfExists('user_exam_tests');
  }
};
