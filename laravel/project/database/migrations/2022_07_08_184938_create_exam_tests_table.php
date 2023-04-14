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
    Schema::create('exam_tests', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->smallInteger('duration')->default(30);
      $table->smallInteger('max_wrong_answers')->default(3);
      $table->boolean('is_valid')->default(true);
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('exam_tests');
  }
};
