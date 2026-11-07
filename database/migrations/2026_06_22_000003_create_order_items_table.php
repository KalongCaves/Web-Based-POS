<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->unsignedBigInteger('menu_id');
      $table->integer('quantity')->default(1);
      $table->decimal('unit_price', 12, 2)->default(0);
      $table->decimal('subtotal', 12, 2)->default(0);
      $table->timestamps();

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->foreign('menu_id')->references('id')->on('menus')->onDelete('restrict');
    });
  }

  public function down()
  {
    Schema::dropIfExists('order_items');
  }
};
