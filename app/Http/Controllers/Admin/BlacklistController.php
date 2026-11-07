<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blacklist;

class BlacklistController extends Controller
{
  public function index()
  {
    return response()->json(Blacklist::orderBy('id', 'desc')->get());
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'phone' => 'required|string|max:50',
      'name' => 'nullable|string|max:191',
      'description' => 'nullable|string|max:500',
    ]);

    $phone = preg_replace('/[^0-9]/', '', $data['phone']);
    $black = Blacklist::create([
      'phone' => $phone,
      'blacklistNoHp' => $phone,
      'blacklistNama' => $data['name'] ?? null,
      'blacklistDesc' => $data['description'] ?? null,
      'blacklistDate' => now(),
    ]);

    return response()->json($black, 201);
  }

  public function destroy($id)
  {
    $item = Blacklist::find($id);
    if (!$item) return response()->json(['message' => 'Not found'], 404);
    $item->delete();
    return response()->json(['message' => 'Deleted']);
  }
}
