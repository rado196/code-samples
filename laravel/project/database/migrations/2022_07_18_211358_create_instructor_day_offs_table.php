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
    Schema::create('instructor_day_offs', function (Blueprint $table) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('instructor_id');
      $table->date('date');
      $table->boolean('is_full_day')->default(false);
      $table->time('start_time')->nullable();
      $table->time('end_time')->nullable();
      $table->timestamps();

      $table
        ->foreign('instructor_id')
        ->references('id')
        ->on('users')
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
    Schema::dropIfExists('instructor_day_offs');
  }
};
