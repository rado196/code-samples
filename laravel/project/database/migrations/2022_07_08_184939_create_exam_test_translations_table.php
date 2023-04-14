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
    Schema::create('exam_test_translations', function (Blueprint $table) {
      $table->id()->startingValue(6546811);
      $table->unsignedBigInteger('exam_test_id');
      $table->unsignedBigInteger('language_id');
      $table->string('title');
      $table->timestamps();

      $table
        ->foreign('exam_test_id')
        ->references('id')
        ->on('exam_tests')
        ->onDelete('cascade');

      $table
        ->foreign('language_id')
        ->references('id')
        ->on('languages')
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
    Schema::dropIfExists('exam_test_translations');
  }
};
