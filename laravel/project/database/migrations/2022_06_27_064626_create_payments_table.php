<?php

use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  private $providers = [
    Payment::PROVIDER_AMERIA_BANK,
    Payment::PROVIDER_ARCA,
    Payment::PROVIDER_IDRAM,
  ];

  private $currencies = [
    Payment::CURRENCY_AMD,
    Payment::CURRENCY_EUR,
    Payment::CURRENCY_USD,
    Payment::CURRENCY_RUB,
  ];

  private $statuses = [
    Payment::STATUS_PENDING,
    Payment::STATUS_SUCCESS,
    Payment::STATUS_FAILURE,
    Payment::STATUS_REFUNDED,
    Payment::STATUS_EXPIRED,
  ];

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->id()->startingValue(2150554);
      $table->enum('provider', $this->providers);
      $table->bigInteger('user_id')->unsigned();
      $table->bigInteger('wallet_id')->unsigned();
      $table->double('amount');
      $table->double('bonus');
      $table->double('amount_with_bonus');
      $table
        ->enum('currency', $this->currencies)
        ->default(Payment::CURRENCY_AMD);
      $table->enum('status', $this->statuses)->default(Payment::STATUS_PENDING);
      $table->string('description');
      $table->string('order_id')->nullable();
      $table->string('transaction_id', 36);
      $table->string('provider_transaction_id')->nullable();
      $table->string('provider_response_code')->nullable();
      $table->string('provider_response_message')->nullable();
      $table->string('provider_description')->nullable();
      $table->timestamps();

      $table
        ->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table
        ->foreign('wallet_id')
        ->references('id')
        ->on('wallets')
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
    Schema::dropIfExists('payments');
  }
};
