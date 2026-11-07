<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('orders', function (Blueprint $table) {
      if (!Schema::hasColumn('orders', 'qris_image_path')) {
        $table->string('qris_image_path')->nullable()->after('status');
      }
      if (!Schema::hasColumn('orders', 'payment_proof_path')) {
        $table->string('payment_proof_path')->nullable()->after('qris_image_path');
      }
    });
  }

  public function down()
  {
    Schema::table('orders', function (Blueprint $table) {
      if (Schema::hasColumn('orders', 'payment_proof_path')) {
        $table->dropColumn('payment_proof_path');
      }
      if (Schema::hasColumn('orders', 'qris_image_path')) {
        $table->dropColumn('qris_image_path');
      }
    });
  }
};
