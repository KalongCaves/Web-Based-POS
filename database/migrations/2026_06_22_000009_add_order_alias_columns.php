<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up()
  {
    Schema::table('orders', function (Blueprint $table) {
      if (!Schema::hasColumn('orders', 'nama_customer')) $table->string('nama_customer')->nullable()->after('admin_id');
      if (!Schema::hasColumn('orders', 'customer_no_hp')) $table->string('customer_no_hp')->nullable()->after('nama_customer');
      if (!Schema::hasColumn('orders', 'customer_alamat')) $table->string('customer_alamat')->nullable()->after('customer_no_hp');
      if (!Schema::hasColumn('orders', 'no_table')) $table->integer('no_table')->nullable()->after('customer_alamat');
      if (!Schema::hasColumn('orders', 'order_price')) $table->decimal('order_price', 12, 2)->default(0)->after('no_table');
    });

    // copy existing data into aliases
    if (Schema::hasTable('orders')) {
      DB::table('orders')->whereNotNull('customer_name')->update(['nama_customer' => DB::raw('customer_name')]);
      DB::table('orders')->whereNotNull('customer_phone')->update(['customer_no_hp' => DB::raw('customer_phone')]);
      DB::table('orders')->whereNotNull('customer_address')->update(['customer_alamat' => DB::raw('customer_address')]);
      DB::table('orders')->whereNotNull('total_price')->update(['order_price' => DB::raw('total_price')]);
      DB::table('orders')->whereNotNull('table_no')->update(['no_table' => DB::raw('table_no')]);
    }
  }

  public function down()
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropColumn(['nama_customer', 'customer_no_hp', 'customer_alamat', 'no_table', 'order_price']);
    });
  }
};
