<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;

  // keep only the canonical/alias columns you specified
  protected $fillable = ['admin_id', 'total_price', 'status', 'nama_customer', 'customer_no_hp', 'customer_alamat', 'no_table', 'qris_image_path', 'payment_proof_path'];

  protected $appends = ['qris_image_url', 'payment_proof_url'];

  public function items()
  {
    return $this->hasMany(\App\Models\DetailOrder::class);
  }

  public function detailOrders()
  {
    return $this->hasMany(\App\Models\DetailOrder::class, 'order_id');
  }

  public function getQrisImageUrlAttribute()
  {
    return $this->qris_image_path ? asset('storage/' . $this->qris_image_path) : null;
  }

  public function getPaymentProofUrlAttribute()
  {
    return $this->payment_proof_path ? asset('storage/' . $this->payment_proof_path) : null;
  }
}
