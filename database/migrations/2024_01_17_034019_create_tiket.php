<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('tickets', function (Blueprint $table) {
      $table->integer('id', true);
      $table->string('booking_code')->nullable();
      $table->integer('queue_number')->nullable();
      $table->integer('customer_id')->nullable();
      $table->integer('counter_category_id')->nullable();
      $table->integer('ticket_category_id')->nullable();
      $table->integer('position')->nullable();
      $table->integer('counter_id')->nullable();
      $table->integer('status')->default(2);
      $table->integer('duration')->nullable();
      $table->string('note')->nullable();
      $table->string('counter_category_code')->nullable();
      $table->string('ticket_category_name')->nullable();
      $table->string('counter_name')->nullable();
      $table->string('group_name')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tickets');
  }
};
