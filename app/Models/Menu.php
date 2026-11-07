<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'menus';
  protected $fillable = ['name', 'category', 'price', 'stock', 'description', 'image_path'];

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class, 'menu_id');
  }
}
