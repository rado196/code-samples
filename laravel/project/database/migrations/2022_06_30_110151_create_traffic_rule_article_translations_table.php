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
    Schema::create('traffic_rule_article_translations', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(6546811);
      $table->bigInteger('article_id')->unsigned();
      $table->bigInteger('language_id')->unsigned();
      $table->string('title');
      $table->timestamps();

      $table
        ->foreign('article_id')
        ->references('id')
        ->on('traffic_rule_articles')
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
    Schema::dropIfExists('traffic_rule_article_translations');
  }
};
