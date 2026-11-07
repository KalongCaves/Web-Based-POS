<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('menus', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('category')->nullable();
      $table->decimal('price', 12, 2)->default(0);
      $table->integer('stock')->default(0);
      $table->text('description')->nullable();
      $table->string('image_path')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('menus');
  }
};
