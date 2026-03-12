<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->get();

        return response()->json($menuItems);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'is_available' => 'boolean'
        ]);

        $menuItem = MenuItem::create($validated);

        return response()->json([
            'message' => 'Menu item created successfully',
            'menu_item' => $menuItem->load('category')
        ], 201);
    }

    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem->load('category'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|string',
            'stock_quantity' => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'sometimes|integer|min:1',
            'is_available' => 'sometimes|boolean'
        ]);

        $menuItem->update($validated);

        return response()->json([
            'message' => 'Menu item updated successfully',
            'menu_item' => $menuItem->load('category')
        ]);
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return response()->json(['message' => 'Menu item deleted successfully']);
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->is_available = !$menuItem->is_available;
        $menuItem->save();

        return response()->json([
            'message' => 'Availability toggled successfully',
            'is_available' => $menuItem->is_available
        ]);
    }

    public function lowStock()
    {
        $items = MenuItem::with('category')
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->where('is_available', true)
            ->get();

        return response()->json($items);
    }
}