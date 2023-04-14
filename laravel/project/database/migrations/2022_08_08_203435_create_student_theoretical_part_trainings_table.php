<?php

use App\Models\StudentTheoreticalPartTraining;
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
    Schema::create('student_theoretical_part_trainings', function (
      Blueprint $table
    ) {
      $table->id()->startingValue(6100010);
      $table->unsignedBigInteger('student_id');
      $table
        ->enum('status', StudentTheoreticalPartTraining::TRAINING_STATUSES)
        ->default(StudentTheoreticalPartTraining::STATUS_PAID);
      $table->date('expiration_date')->nullable();
      $table->timestamps();

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
    Schema::dropIfExists('student_theoretical_part_trainings');
  }
};
