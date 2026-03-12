<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'cashier', 'items.menuItem']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter by user (customer)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,online',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Calculate total and validate stock
            $totalAmount = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                // Check stock
                if ($menuItem->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$menuItem->name}. Available: {$menuItem->stock_quantity}");
                }

                $subtotal = $menuItem->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'subtotal' => $subtotal
                ];
            }

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'cashier_id' => auth()->user()->isCashier() ? auth()->id() : null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null
            ]);

            // Create order items and update stock
            foreach ($orderItems as $item) {
                $order->items()->create($item);

                // Update stock
                $menuItem = MenuItem::find($item['menu_item_id']);
                $menuItem->updateStock(
                    -$item['quantity'],
                    'sale',
                    'order',
                    $order->id,
                    auth()->id()
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items.menuItem', 'user'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(Order $order)
    {
        return response()->json($order->load(['user', 'cashier', 'items.menuItem.category']));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled'
        ]);

        $order->updateStatus($validated['status']);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    public function queue()
    {
        $orders = Order::with(['items.menuItem'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($orders);
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        $query = Order::with(['items.menuItem'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return response()->json($orders);
    }
}
