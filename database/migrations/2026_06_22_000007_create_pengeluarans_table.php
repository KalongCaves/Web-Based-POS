<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::create('pengeluarans', function (Blueprint $table) {
      $table->id();
      $table->string('expenseDesc')->nullable();
      $table->string('expenseCategory')->nullable();
      $table->decimal('expenseAmount', 12, 2)->default(0);
      $table->date('expenseDate')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('pengeluarans');
  }
};
