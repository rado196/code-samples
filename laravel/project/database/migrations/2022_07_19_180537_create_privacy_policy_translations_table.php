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
    Schema::create('privacy_policy_translations', function (Blueprint $table) {
      $table->id()->startingValue(6546811);
      $table->bigInteger('privacy_policy_id')->unsigned();
      $table->bigInteger('language_id')->unsigned();
      $table->text('content');
      $table->timestamps();

      $table
        ->foreign('privacy_policy_id')
        ->references('id')
        ->on('privacy_policies')
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
    Schema::dropIfExists('privacy_policy_translations');
  }
};
