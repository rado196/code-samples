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
    Schema::create('vehicle_sign_translations', function (Blueprint $table) {
      $table->id()->startingValue(6546811);
      $table->bigInteger('vehicle_sign_id')->unsigned();
      $table->bigInteger('language_id')->unsigned();
      $table->text('content');
      $table->timestamps();

      $table
        ->foreign('vehicle_sign_id')
        ->references('id')
        ->on('vehicle_signs')
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
    Schema::dropIfExists('vehicle_sign_translations');
  }
};
