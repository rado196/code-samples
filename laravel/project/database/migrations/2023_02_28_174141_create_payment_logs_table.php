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
    Schema::create('payment_logs', function (Blueprint $table) {
      $table->id();
      $table
        ->bigInteger('user_id')
        ->unsigned()
        ->nullable();
      $table->string('url');
      $table->string('http_method');
      $table->json('request_body');
      $table->json('request_headers');
      $table->json('response_body')->nullable();
      $table->json('response_headers')->nullable();
      $table->integer('status_code')->nullable();
      $table->timestamps();

      $table
        ->foreign('user_id')
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
    Schema::dropIfExists('payment_logs');
  }
};
