<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'group_by' => 'sometimes|in:day,week,month'
        ]);

        $groupBy = $validated['group_by'] ?? 'day';

        $query = Order::whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
            ->where('status', 'completed');

        // Daily sales
        if ($groupBy === 'day') {
            $sales = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        }

        // Weekly sales
        if ($groupBy === 'week') {
            $sales = $query->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();
        }

        // Monthly sales
        if ($groupBy === 'month') {
            $sales = $query->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        }

        return response()->json($sales);
    }

    public function bestSelling(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        $limit = $validated['limit'] ?? 10;

        $items = OrderItem::select(
                'menu_item_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', function($query) use ($validated) {
                $query->whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                    ->where('status', 'completed');
            })
            ->groupBy('menu_item_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->with('menuItem')
            ->get();

        return response()->json($items);
    }

    public function categoryBreakdown(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date'
        ]);

        $categories = DB::table('categories')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(order_items.quantity) as items_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('menu_items', 'categories.id', '=', 'menu_items.category_id')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$validated['from_date'], $validated['to_date']])
            ->where('orders.status', 'completed')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return response()->json($categories);
    }

    public function orderTrend(Request $request)
    {
        $validated = $request->validate([
            'days' => 'sometimes|integer|min:7|max:90'
        ]);

        $days = $validated['days'] ?? 30;

        $trend = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trend);
    }

    public function summary(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date'
        ]);

        $summary = [
            'total_orders' => Order::whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                ->where('status', 'completed')
                ->count(),
            
            'total_revenue' => Order::whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                ->where('status', 'completed')
                ->sum('total_amount'),
            
            'average_order_value' => Order::whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                ->where('status', 'completed')
                ->avg('total_amount'),
            
            'total_items_sold' => OrderItem::whereHas('order', function($query) use ($validated) {
                $query->whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                    ->where('status', 'completed');
            })->sum('quantity'),
            
            'unique_customers' => Order::whereBetween('created_at', [$validated['from_date'], $validated['to_date']])
                ->where('status', 'completed')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return response()->json($summary);
    }
}