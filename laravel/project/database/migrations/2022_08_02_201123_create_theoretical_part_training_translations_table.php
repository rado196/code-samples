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
    Schema::create('theoretical_part_training_translations', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(6546811);
      $table->unsignedBigInteger('training_id');
      $table->unsignedBigInteger('language_id');
      $table->string('title');
      $table->text('description')->nullable();
      $table->timestamps();

      $table
        ->foreign('training_id')
        ->references('id')
        ->on('theoretical_part_trainings')
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
    Schema::dropIfExists('theoretical_part_training_translations');
  }
};
