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
    Schema::create('theoretical_part_lessons', function (Blueprint $table) {
      $table->id()->startingValue(545255);
      $table->unsignedBigInteger('exam_group_id')->nullable();
      $table->timestamps();

      $table
        ->foreign('exam_group_id')
        ->references('id')
        ->on('exam_groups')
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
    Schema::dropIfExists('theoretical_part_lessons');
  }
};
