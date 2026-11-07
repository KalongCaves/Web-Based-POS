<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
  use HasFactory;

  protected $table = 'detail_orders';

  protected $fillable = ['order_id', 'order_type', 'order_status', 'order_date', 'menu_id', 'order_item_quantity', 'order_subtotal'];

  protected $appends = ['quantity', 'unit_price', 'line_total'];

  public function getQuantityAttribute()
  {
    return $this->order_item_quantity;
  }

  public function getUnitPriceAttribute()
  {
    if ($this->order_item_quantity && $this->order_item_quantity > 0) {
      return $this->order_subtotal / $this->order_item_quantity;
    }
    return 0;
  }

  public function getLineTotalAttribute()
  {
    return $this->order_subtotal;
  }

  public function order()
  {
    return $this->belongsTo(Order::class);
  }

  public function menu()
  {
    return $this->belongsTo(Menu::class, 'menu_id')->withTrashed();
  }
}
