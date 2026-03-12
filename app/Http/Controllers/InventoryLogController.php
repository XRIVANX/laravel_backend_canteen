<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function logs(Request $request)
    {
        $query = InventoryLog::with(['menuItem', 'creator']);

        if ($request->has('menu_item_id')) {
            $query->where('menu_item_id', $request->menu_item_id);
        }

        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($logs);
    }

    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer',
            'reason' => 'required|in:restock,adjustment',
            'notes' => 'nullable|string'
        ]);

        $menuItem = MenuItem::findOrFail($validated['menu_item_id']);

        $menuItem->updateStock(
            $validated['quantity'],
            $validated['reason'],
            'manual',
            null,
            auth()->id()
        );

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'menu_item' => $menuItem->fresh()
        ]);
    }

    public function bulkRestock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $results = [];

        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            $menuItem->updateStock(
                $item['quantity'],
                'restock',
                'bulk',
                null,
                auth()->id()
            );
            $results[] = $menuItem->fresh();
        }

        return response()->json([
            'message' => 'Bulk restock completed successfully',
            'items' => $results
        ]);
    }
}