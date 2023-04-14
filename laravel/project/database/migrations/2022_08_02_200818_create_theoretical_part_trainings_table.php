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
    Schema::create('theoretical_part_trainings', function (Blueprint $table) {
      $table->id()->startingValue(2262558);
      $table->unsignedBigInteger('lesson_id');
      $table->string('image')->nullable();
      $table->timestamps();

      $table
        ->foreign('lesson_id')
        ->references('id')
        ->on('theoretical_part_lessons')
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
    Schema::dropIfExists('theoretical_part_trainings');
  }
};
