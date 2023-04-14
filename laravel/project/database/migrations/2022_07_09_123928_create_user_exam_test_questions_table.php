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
    Schema::create('user_exam_test_questions', function (Blueprint $table) {
      $table->id()->startingValue(13451200541);
      $table->unsignedBigInteger('user_exam_test_id');
      $table->unsignedBigInteger('exam_test_question_id');
      $table->unsignedBigInteger('exam_test_answer_id')->nullable();
      $table->boolean('is_right')->default(false);
      $table->timestamps();

      $table
        ->foreign('user_exam_test_id')
        ->references('id')
        ->on('user_exam_tests')
        ->onDelete('cascade');

      $table
        ->foreign('exam_test_question_id')
        ->references('id')
        ->on('exam_test_questions')
        ->onDelete('cascade');

      $table
        ->foreign('exam_test_answer_id')
        ->references('id')
        ->on('exam_test_answers')
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
    Schema::dropIfExists('user_exam_test_questions');
  }
};
