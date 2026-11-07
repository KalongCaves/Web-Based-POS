<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up()
  {
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

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->foreign('menu_id')->references('id')->on('menus')->onDelete('restrict');
    });

    // migrate existing order_items into detail_orders if table exists
    if (Schema::hasTable('order_items')) {
      $rows = DB::table('order_items')->get();
      foreach ($rows as $r) {
        DB::table('detail_orders')->insert([
          'order_id' => $r->order_id,
          'order_type' => 'purchase',
          'order_status' => null,
          'order_date' => $r->created_at,
          'menu_id' => $r->menu_id,
          'order_item_quantity' => $r->quantity,
          'order_subtotal' => $r->subtotal,
          'created_at' => $r->created_at,
          'updated_at' => $r->updated_at,
        ]);
      }
      Schema::dropIfExists('order_items');
    }
  }

  public function down()
  {
    // recreate order_items from detail_orders if needed
    if (Schema::hasTable('detail_orders')) {
      $rows = DB::table('detail_orders')->get();
      Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->unsignedBigInteger('menu_id');
        $table->integer('quantity')->default(1);
        $table->decimal('unit_price', 12, 2)->default(0);
        $table->decimal('subtotal', 12, 2)->default(0);
        $table->timestamps();
      });
      foreach ($rows as $r) {
        DB::table('order_items')->insert([
          'order_id' => $r->order_id,
          'menu_id' => $r->menu_id,
          'quantity' => $r->order_item_quantity,
          'unit_price' => 0,
          'subtotal' => $r->order_subtotal,
          'created_at' => $r->order_date ?? $r->created_at,
          'updated_at' => $r->updated_at,
        ]);
      }
      Schema::dropIfExists('detail_orders');
    }
  }
};
