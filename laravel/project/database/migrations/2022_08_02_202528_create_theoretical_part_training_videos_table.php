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
    Schema::create('theoretical_part_training_videos', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(2262558);
      $table->unsignedBigInteger('training_id');
      $table->string('name')->nullable();
      $table->string('poster')->nullable();
      $table->timestamps();

      $table
        ->foreign('training_id')
        ->references('id')
        ->on('theoretical_part_trainings')
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
    Schema::dropIfExists('theoretical_part_training_videos');
  }
};
