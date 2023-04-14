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
    Schema::create('instructor_work_details', function (Blueprint $table) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('instructor_id');
      $table->integer('driving_course_hourly_price')->default(0);
      $table->boolean('hidden')->default(false);
      $table->boolean('monday')->default(false);
      $table->boolean('tuesday')->default(false);
      $table->boolean('wednesday')->default(false);
      $table->boolean('thursday')->default(false);
      $table->boolean('friday')->default(false);
      $table->boolean('saturday')->default(false);
      $table->boolean('sunday')->default(false);
      $table->time('monday_start_time')->nullable();
      $table->time('monday_end_time')->nullable();
      $table->time('tuesday_start_time')->nullable();
      $table->time('tuesday_end_time')->nullable();
      $table->time('wednesday_start_time')->nullable();
      $table->time('wednesday_end_time')->nullable();
      $table->time('thursday_start_time')->nullable();
      $table->time('thursday_end_time')->nullable();
      $table->time('friday_start_time')->nullable();
      $table->time('friday_end_time')->nullable();
      $table->time('saturday_start_time')->nullable();
      $table->time('saturday_end_time')->nullable();
      $table->time('sunday_start_time')->nullable();
      $table->time('sunday_end_time')->nullable();
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
    Schema::dropIfExists('instructor_work_details');
  }
};
