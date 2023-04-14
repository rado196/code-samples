<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('payments', function (Blueprint $table) {
      DB::statement(
        'ALTER TABLE `payments`
                    MODIFY COLUMN `provider` ENUM("AmeriaBank", "ArCa", "IDram", "EasyPay")'
      );
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('payments', function (Blueprint $table) {
      DB::statement(
        'ALTER TABLE `payments`
                  MODIFY COLUMN `provider` ENUM("AmeriaBank", "ArCa", "IDram")'
      );
    });
  }
};
