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
    Schema::create('exam_test_questions', function (Blueprint $table) {
      $table->id()->startingValue(801200);
      $table->unsignedBigInteger('exam_test_id');
      $table->unsignedBigInteger('exam_group_question_id');
      $table->unsignedBigInteger('explanation_id');
      $table->timestamps();

      $table
        ->foreign('exam_test_id')
        ->references('id')
        ->on('exam_tests')
        ->onDelete('cascade');

      $table
        ->foreign('exam_group_question_id')
        ->references('id')
        ->on('exam_group_questions')
        ->onDelete('cascade');

      $table
        ->foreign('explanation_id')
        ->references('id')
        ->on('exam_group_explanations')
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
    Schema::dropIfExists('exam_test_questions');
  }
};
