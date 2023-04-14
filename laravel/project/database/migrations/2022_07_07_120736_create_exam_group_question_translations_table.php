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
    Schema::create('exam_group_question_translations', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(6546811);
      $table->unsignedBigInteger('exam_group_question_id');
      $table->unsignedBigInteger('language_id');
      $table->text('title');
      $table->timestamps();

      $table
        ->foreign('exam_group_question_id')
        ->references('id')
        ->on('exam_group_questions')
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
    Schema::dropIfExists('exam_group_question_translations');
  }
};
