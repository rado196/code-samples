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
    Schema::create('malfunction_lists', function (Blueprint $table) {
      $table->id()->startingValue(321200);
      $table->bigInteger('malfunction_list_article_id')->unsigned();
      $table->timestamps();

      $table
        ->foreign('malfunction_list_article_id')
        ->references('id')
        ->on('malfunction_list_articles')
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
    Schema::dropIfExists('malfunction_lists');
  }
};
