<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    if (!Schema::hasTable('orders')) {
      Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->string('customer_name')->nullable();
        $table->string('customer_phone')->nullable();
        $table->string('customer_address')->nullable();
        $table->integer('table_no')->nullable();
        $table->decimal('total_price', 12, 2)->default(0);
        $table->string('status')->default('pending');
        // ERD alias columns
        $table->string('nama_customer')->nullable();
        $table->string('customer_no_hp')->nullable();
        $table->string('customer_alamat')->nullable();
        $table->integer('no_table')->nullable();
        $table->decimal('order_price', 12, 2)->default(0);
        $table->timestamps();
      });

      return;
    }

    // Add missing columns if absent
    if (!Schema::hasColumn('orders', 'admin_id')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->unsignedBigInteger('admin_id')->nullable()->after('id');
      });
    }

    if (!Schema::hasColumn('orders', 'customer_name')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_name')->nullable()->after('admin_id');
      });
    }

    if (!Schema::hasColumn('orders', 'customer_phone')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_phone')->nullable()->after('customer_name');
      });
    }

    if (!Schema::hasColumn('orders', 'customer_address')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_address')->nullable()->after('customer_phone');
      });
    }

    if (!Schema::hasColumn('orders', 'table_no')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->integer('table_no')->nullable()->after('customer_address');
      });
    }

    if (!Schema::hasColumn('orders', 'total_price')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->decimal('total_price', 12, 2)->default(0)->after('table_no');
      });
    }

    if (!Schema::hasColumn('orders', 'status')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('status')->default('pending')->after('total_price');
      });
    }

    // Alias columns used in model
    if (!Schema::hasColumn('orders', 'nama_customer')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('nama_customer')->nullable()->after('status');
      });
    }

    if (!Schema::hasColumn('orders', 'customer_no_hp')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_no_hp')->nullable()->after('nama_customer');
      });
    }

    if (!Schema::hasColumn('orders', 'customer_alamat')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->string('customer_alamat')->nullable()->after('customer_no_hp');
      });
    }

    if (!Schema::hasColumn('orders', 'no_table')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->integer('no_table')->nullable()->after('customer_alamat');
      });
    }

    if (!Schema::hasColumn('orders', 'order_price')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->decimal('order_price', 12, 2)->default(0)->after('no_table');
      });
    }

    if (!Schema::hasColumn('orders', 'created_at') || !Schema::hasColumn('orders', 'updated_at')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->timestamps();
      });
    }
  }

  public function down()
  {
    if (!Schema::hasTable('orders')) return;

    $cols = ['order_price', 'no_table', 'customer_alamat', 'customer_no_hp', 'nama_customer', 'status', 'total_price', 'table_no', 'customer_address', 'customer_phone', 'customer_name', 'admin_id'];
    foreach ($cols as $c) {
      if (Schema::hasColumn('orders', $c)) {
        Schema::table('orders', function (Blueprint $table) use ($c) {
          $table->dropColumn($c);
        });
      }
    }

    if (Schema::hasColumn('orders', 'created_at') && Schema::hasColumn('orders', 'updated_at')) {
      Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['created_at', 'updated_at']);
      });
    }
  }
};
