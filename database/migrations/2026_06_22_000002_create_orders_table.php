<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('admin_id')->nullable();
      $table->string('customer_name')->nullable();
      $table->string('customer_phone')->nullable();
      $table->string('customer_address')->nullable();
      $table->integer('table_no')->nullable();
      $table->decimal('total_price', 12, 2)->default(0);
      $table->string('status')->default('pending');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('orders');
  }
};
