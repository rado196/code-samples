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
    Schema::create('exam_group_explanations', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->unsignedBigInteger('exam_group_id');
      $table->unsignedBigInteger('question_id');
      $table->timestamps();

      $table
        ->foreign('exam_group_id')
        ->references('id')
        ->on('exam_groups')
        ->onDelete('cascade');

      $table
        ->foreign('question_id')
        ->references('id')
        ->on('exam_group_questions')
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
    Schema::dropIfExists('exam_group_explanations');
  }
};
