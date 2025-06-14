<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // جلب كل الطلبات (مثلاً للإدارة)
    public function index()
    {
        return Order::with('product')->get();
    }

    // إضافة طلب جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'address' => 'required|string',
        ]);

        $order = Order::create([
            'product_id' => $validated['product_id'],
            'user_id' => $request->user()->id,
            'customer_name' => $validated['customer_name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'address' => $validated['address'],
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order
        ], 201);
    }
}
