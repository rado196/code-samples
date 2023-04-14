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
    Schema::table('traffic_rule_article_translations', function (
      Blueprint $table
    ) {
      $table->text('title')->change();
    });

    Schema::table('road_sign_article_translations', function (
      Blueprint $table
    ) {
      $table->text('title')->change();
    });

    Schema::table('road_marking_article_translations', function (
      Blueprint $table
    ) {
      $table->text('title')->change();
    });

    Schema::table('malfunction_list_article_translations', function (
      Blueprint $table
    ) {
      $table->text('title')->change();
    });

    Schema::table('road_safety_law_article_translations', function (
      Blueprint $table
    ) {
      $table->text('title')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
};
