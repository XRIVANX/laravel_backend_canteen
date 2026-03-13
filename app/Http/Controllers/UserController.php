<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all customers (users with role 'customer')
     */
    public function getCustomers()
    {
        try {
            $customers = User::where('role', 'customer')
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customers by name or email
     */
    public function searchCustomers(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            $customers = User::where('role', 'customer')
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->limit(20)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single customer by ID
     */
    public function getCustomer($id)
    {
        try {
            $customer = User::where('role', 'customer')
                ->where('id', $id)
                ->select('id', 'name', 'email')
                ->first();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
