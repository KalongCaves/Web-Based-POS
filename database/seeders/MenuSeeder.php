<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
  public function run()
  {
    $items = [
      ['id' => 'classic-potato-chips', 'title' => 'Classic Potato Chips', 'description' => 'Crispy and salty potato chips.', 'badge' => 'Chips', 'category' => 'Chips', 'price' => 1.50, 'stock' => 50, 'icon' => '🥔'],
      ['id' => 'chocolate-bar', 'title' => 'Chocolate Bar', 'description' => 'Smooth milk chocolate with a rich bite.', 'badge' => 'Candy', 'category' => 'Candy', 'price' => 2.00, 'stock' => 30, 'icon' => '🍫'],
      ['id' => 'energy-drink', 'title' => 'Energy Drink', 'description' => 'Boost your energy with a refreshing sip.', 'badge' => 'Beverages', 'category' => 'Beverages', 'price' => 3.00, 'stock' => 25, 'icon' => '🥤'],
      ['id' => 'cookies', 'title' => 'Cookies', 'description' => 'Freshly baked chocolate chip cookies.', 'badge' => 'Baked Goods', 'category' => 'Snacks', 'price' => 2.50, 'stock' => 40, 'icon' => '🍪'],
      ['id' => 'pretzels', 'title' => 'Pretzels', 'description' => 'Salted twisted pretzels with crunchy texture.', 'badge' => 'Snacks', 'category' => 'Chips', 'price' => 1.75, 'stock' => 35, 'icon' => '🥨'],
      ['id' => 'gummy-bears', 'title' => 'Gummy Bears', 'description' => 'Colorful fruit-flavored gummies.', 'badge' => 'Candy', 'category' => 'Candy', 'price' => 1.25, 'stock' => 60, 'icon' => '🧸'],
      ['id' => 'bottled-water', 'title' => 'Bottled Water', 'description' => 'Pure mineral water to stay refreshed.', 'badge' => 'Beverages', 'category' => 'Beverages', 'price' => 1.00, 'stock' => 100, 'icon' => '💧'],
      ['id' => 'popcorn', 'title' => 'Popcorn', 'description' => 'Buttery popcorn for crunchy snacks.', 'badge' => 'Baked Goods', 'category' => 'Snacks', 'price' => 2.25, 'stock' => 20, 'icon' => '🍿'],
    ];

    foreach ($items as $it) {
      Menu::create([
        'name' => $it['title'],
        'category' => $it['category'] ?? null,
        'price' => ($it['price'] * 1000),
        'stock' => $it['stock'] ?? 0,
        'description' => $it['description'] ?? null,
        'image_path' => null,
      ]);
    }
  }
}
