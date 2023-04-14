<?php

use App\Models\StudentAppointment;
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
    Schema::create('student_appointments', function (Blueprint $table) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('student_id');
      $table->unsignedBigInteger('instructor_id');
      $table->date('date');
      $table->time('start_time');
      $table->time('end_time');
      $table->float('duration');
      $table->unsignedMediumInteger('price');
      $table
        ->enum('status', StudentAppointment::APPOINTMENT_STATUSES)
        ->default(StudentAppointment::STATUS_BOOKED);
      $table->timestamps();

      $table
        ->foreign('instructor_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table
        ->foreign('student_id')
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
    Schema::dropIfExists('student_appointments');
  }
};
