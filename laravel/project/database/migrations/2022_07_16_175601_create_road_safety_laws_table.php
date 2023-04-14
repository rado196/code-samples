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
    Schema::create('road_safety_laws', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->bigInteger('road_safety_law_article_id')->unsigned();
      $table->timestamps();

      $table
        ->foreign('road_safety_law_article_id')
        ->references('id')
        ->on('road_safety_law_articles')
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
    Schema::dropIfExists('road_safety_laws');
  }
};
