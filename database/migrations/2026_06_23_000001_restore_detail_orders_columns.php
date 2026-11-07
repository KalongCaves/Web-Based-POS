<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up()
  {
    // If table doesn't exist, recreate with expected schema
    if (!Schema::hasTable('detail_orders')) {
      Schema::create('detail_orders', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->string('order_type')->nullable();
        $table->string('order_status')->nullable();
        $table->timestamp('order_date')->nullable();
        $table->unsignedBigInteger('menu_id');
        $table->integer('order_item_quantity')->default(1);
        $table->decimal('order_subtotal', 12, 2)->default(0);
        $table->timestamps();
      });

      // Add foreign keys where supported
      $driver = Schema::getConnection()->getDriverName();
      if ($driver !== 'sqlite') {
        try {
          Schema::table('detail_orders', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('restrict');
          });
        } catch (\Exception $e) {
          // ignore if constraints cannot be added
        }
      }

      return;
    }

    // Table exists: ensure expected columns exist
    if (!Schema::hasColumn('detail_orders', 'order_type')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->string('order_type')->nullable()->after('order_id');
      });
    }

    if (!Schema::hasColumn('detail_orders', 'order_status')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->string('order_status')->nullable()->after('order_type');
      });
    }

    if (!Schema::hasColumn('detail_orders', 'order_date')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->timestamp('order_date')->nullable()->after('order_status');
      });
    }

    if (!Schema::hasColumn('detail_orders', 'menu_id')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->unsignedBigInteger('menu_id')->after('order_date');
      });
    }

    if (!Schema::hasColumn('detail_orders', 'order_item_quantity')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->integer('order_item_quantity')->default(1)->after('menu_id');
      });
    }

    if (!Schema::hasColumn('detail_orders', 'order_subtotal')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->decimal('order_subtotal', 12, 2)->default(0)->after('order_item_quantity');
      });
    }

    // Attempt to add foreign key constraints when supported and if not already present
    $driver = Schema::getConnection()->getDriverName();
    if ($driver !== 'sqlite') {
      try {
        // add order_id FK if not present
        $hasOrderFk = false;
        $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'detail_orders' AND CONSTRAINT_TYPE = 'FOREIGN KEY'");
        if ($fks && is_array($fks)) {
          foreach ($fks as $fk) {
            if (property_exists($fk, 'CONSTRAINT_NAME') && $fk->CONSTRAINT_NAME) $hasOrderFk = true;
          }
        }
        if (!$hasOrderFk) {
          Schema::table('detail_orders', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('restrict');
          });
        }
      } catch (\Exception $e) {
        // ignore failures adding constraints
      }
    }
  }

  public function down()
  {
    if (!Schema::hasTable('detail_orders')) return;

    // Remove columns we may have added
    if (Schema::hasColumn('detail_orders', 'order_subtotal')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('order_subtotal');
      });
    }

    if (Schema::hasColumn('detail_orders', 'order_item_quantity')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('order_item_quantity');
      });
    }

    if (Schema::hasColumn('detail_orders', 'menu_id')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('menu_id');
      });
    }

    if (Schema::hasColumn('detail_orders', 'order_date')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('order_date');
      });
    }

    if (Schema::hasColumn('detail_orders', 'order_status')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('order_status');
      });
    }

    if (Schema::hasColumn('detail_orders', 'order_type')) {
      Schema::table('detail_orders', function (Blueprint $table) {
        $table->dropColumn('order_type');
      });
    }
  }
};
