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
    Schema::create('config', function (Blueprint $table) {
      $table->integer('id', true);
      $table->string('logo1')->nullable();
      $table->string('logo2')->nullable();
      $table->string('loading')->nullable();
      $table->string('instance_name')->nullable();
      $table->string('running_text')->nullable();
      $table->integer('color1_id')->nullable();
      $table->integer('color2_id')->nullable();
      $table->integer('color3_id')->nullable();
      $table->integer('status')->default(2);
      $table->integer('partnership')->default(1);
      $table->string('partner_api')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('config');
  }
};
