<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
  use HasFactory;

  // timestamps (created_at/updated_at) were removed from the table; disable automatic timestamps
  public $timestamps = false;

  protected $fillable = ['phone', 'blacklistNoHp', 'blacklistNama', 'blacklistDesc', 'blacklistDate'];
}
