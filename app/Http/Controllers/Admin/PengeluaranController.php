<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
  public function store(Request $request)
  {
    $data = $request->validate([
      'expenseDesc' => 'nullable|string|max:500',
      'expenseCategory' => 'nullable|string|max:191',
      'expenseAmount' => 'required|numeric',
      'expenseDate' => 'nullable|date',
    ]);

    $data['expenseDate'] = $data['expenseDate'] ?? now()->toDateString();

    $e = Pengeluaran::create($data);

    return response()->json(['ok' => true, 'expense' => $e]);
  }

  public function destroy($id)
  {
    $e = Pengeluaran::find($id);
    if (!$e) return response()->json(['ok' => false, 'message' => 'Not found'], 404);
    $e->delete();
    return response()->json(['ok' => true]);
  }
}
