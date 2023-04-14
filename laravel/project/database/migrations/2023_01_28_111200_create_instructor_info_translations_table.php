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
    Schema::create('instructor_info_translations', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('instructor_info_id');
      $table->unsignedBigInteger('language_id');
      $table->text('description');
      $table->timestamps();

      $table
        ->foreign('instructor_info_id')
        ->references('id')
        ->on('instructor_infos')
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
    Schema::dropIfExists('instructor_info_translations');
  }
};
