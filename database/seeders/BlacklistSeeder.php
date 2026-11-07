<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blacklist;

class BlacklistSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Blacklist::updateOrCreate(
      ['phone' => '081583507233'],
      [
        'blacklistNoHp' => '081583507233',
        'blacklistNama' => 'Blocked User',
        'blacklistDesc' => 'Automatically added by seeder for testing',
        'blacklistDate' => now(),
      ]
    );
  }
}
