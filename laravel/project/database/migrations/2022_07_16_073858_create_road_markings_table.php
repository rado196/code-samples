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
    Schema::create('road_markings', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->unsignedBigInteger('road_marking_article_id');
      $table->timestamps();

      $table
        ->foreign('road_marking_article_id')
        ->references('id')
        ->on('road_marking_articles')
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
    Schema::dropIfExists('road_markings');
  }
};
