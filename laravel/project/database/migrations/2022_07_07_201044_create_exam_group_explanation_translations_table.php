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
    Schema::create('exam_group_explanation_translations', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(6546811);
      $table->unsignedBigInteger('explanation_id');
      $table->unsignedBigInteger('language_id');
      $table->text('title');
      $table->text('description');
      $table->timestamps();

      $table
        ->foreign('explanation_id')
        ->references('id')
        ->on('exam_group_explanations')
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
    Schema::dropIfExists('exam_group_explanation_translations');
  }
};
