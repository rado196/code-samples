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
    Schema::create('traffic_rules', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->bigInteger('traffic_rule_article_id')->unsigned();
      $table->timestamps();

      $table
        ->foreign('traffic_rule_article_id')
        ->references('id')
        ->on('traffic_rule_articles')
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
    Schema::dropIfExists('traffic_rules');
  }
};
