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
    Schema::create('road_signs', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->bigInteger('road_sign_article_id')->unsigned();
      $table->timestamps();

      $table
        ->foreign('road_sign_article_id')
        ->references('id')
        ->on('road_sign_articles')
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
    Schema::dropIfExists('road_signs');
  }
};
