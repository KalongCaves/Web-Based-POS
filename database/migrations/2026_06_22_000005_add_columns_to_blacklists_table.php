<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('blacklists', function (Blueprint $table) {
      if (!Schema::hasColumn('blacklists', 'blacklistNoHp')) {
        $table->string('blacklistNoHp')->nullable()->after('phone');
      }
      if (!Schema::hasColumn('blacklists', 'blacklistNama')) {
        $table->string('blacklistNama')->nullable()->after('blacklistNoHp');
      }
      if (!Schema::hasColumn('blacklists', 'blacklistDesc')) {
        $table->string('blacklistDesc')->nullable()->after('blacklistNama');
      }
      if (!Schema::hasColumn('blacklists', 'blacklistDate')) {
        $table->date('blacklistDate')->nullable()->after('blacklistDesc');
      }
    });
  }

  public function down()
  {
    Schema::table('blacklists', function (Blueprint $table) {
      $table->dropColumn(['blacklistNoHp', 'blacklistNama', 'blacklistDesc', 'blacklistDate']);
    });
  }
};
