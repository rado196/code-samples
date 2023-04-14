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
    Schema::create('road_marking_translations', function (Blueprint $table) {
      $table->id()->startingValue(6546811);
      $table->bigInteger('road_marking_id')->unsigned();
      $table->bigInteger('language_id')->unsigned();
      $table->text('content');
      $table->timestamps();

      $table
        ->foreign('road_marking_id')
        ->references('id')
        ->on('road_markings')
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
    Schema::dropIfExists('road_marking_translations');
  }
};
