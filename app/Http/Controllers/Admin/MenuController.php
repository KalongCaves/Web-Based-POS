<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
  public function index()
  {
    try {
      return response()->json(Menu::orderBy('id')->get());
    } catch (\Illuminate\Database\QueryException $e) {
      // likely missing deleted_at column (soft deletes not migrated). Fall back to raw table read.
      $items = \Illuminate\Support\Facades\DB::table('menus')->orderBy('id')->get();
      return response()->json($items);
    }
  }

  public function store(Request $request)
  {
    // allow partial updates for fields like stock (optional on edit)
    $data = $request->validate([
      'name' => 'required|string|max:191',
      'category' => 'nullable|string|max:100',
      'price' => 'required|numeric|min:0',
      'stock' => 'sometimes|integer|min:0',
      'description' => 'nullable|string',
      'image' => 'nullable|image|max:5120'
    ]);

    if ($request->hasFile('image')) {
      $path = $request->file('image')->store('menus', 'public');
      $data['image_path'] = $path;
    }

    $menu = Menu::create($data);

    return response()->json($menu, 201);
  }

  public function update(Request $request, $id)
  {
    $menu = Menu::findOrFail($id);
    // allow partial updates (admin may only change stock)
    $data = $request->validate([
      'name' => 'sometimes|required|string|max:191',
      'category' => 'sometimes|nullable|string|max:100',
      'price' => 'sometimes|required|numeric|min:0',
      'stock' => 'sometimes|integer|min:0',
      'description' => 'sometimes|nullable|string',
      'image' => 'nullable|image|max:5120'
    ]);

    if ($request->hasFile('image')) {
      // delete old image if exists
      if ($menu->image_path) Storage::disk('public')->delete($menu->image_path);
      $path = $request->file('image')->store('menus', 'public');
      $data['image_path'] = $path;
    }

    $menu->update($data);
    return response()->json($menu);
  }

  public function destroy($id)
  {
    $menu = Menu::find($id);
    if (!$menu) return response()->json(['message' => 'Not found'], 404);
    if ($menu->image_path) Storage::disk('public')->delete($menu->image_path);
    try {
      $menu->delete();
    } catch (\Illuminate\Database\QueryException $e) {
      // Handle foreign key constraint (e.g., referenced by order items)
      if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'FOREIGN KEY')) {
        return response()->json(['message' => 'Cannot delete menu item: it is referenced by other records (orders).'], 409);
      }
      return response()->json(['message' => 'Failed to delete menu item', 'error' => $e->getMessage()], 500);
    }

    return response()->json(['message' => 'Deleted']);
  }
}
