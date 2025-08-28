<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewOrderNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\OutOfStockNotification;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])->get();
        return view('admin.orders', compact('orders'));
    }

    // Add new order
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
    public function placeOrder(Request $request)
    {
        $validatedData = $request->validate([
            'cartItems' => 'required|array|min:1',
            'cartItems.*.id' => 'required|exists:products,id',
            'cartItems.*.quantity' => 'required|integer|min:1',
            'shippingInfo' => 'required|array',
            'shippingInfo.firstName' => 'required|string',
            'shippingInfo.lastName' => 'required|string',
            'shippingInfo.email' => 'required|email',
            'shippingInfo.phone' => 'required|string',
            'shippingInfo.address' => 'required|string',
            'shippingInfo.city' => 'required|string',
            'shippingInfo.zipCode' => 'required|string',
        ]);

        try {
            $order = DB::transaction(function () use ($validatedData) {
                $shippingInfo = $validatedData['shippingInfo'];
                $orderTotal = 0;

                // 1. Pre-check stock availability and calculate total
                foreach ($validatedData['cartItems'] as $item) {
                    $product = Product::find($item['id']);
                    if ($product->stock < $item['quantity']) {
                        // Throw an exception to automatically roll back the transaction
                        throw new \Exception('Not enough stock for ' . $product->name);
                    }
                    $orderTotal += $product->price * $item['quantity'];
                }

                // 2. Create the main order record
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'customer_name' => $shippingInfo['firstName'] . ' ' . $shippingInfo['lastName'],
                    'email' => $shippingInfo['email'],
                    'mobile' => $shippingInfo['phone'],
                    'address' => $shippingInfo['address'] . ', ' . $shippingInfo['city'] . ', ' . $shippingInfo['zipCode'],
                    'status' => 'pending',
                    'total' => $orderTotal,
                ]);

                // 3. Create order items and decrement stock
                foreach ($validatedData['cartItems'] as $item) {
                    $product = Product::find($item['id']); // Re-fetch to be safe
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);
                    $product->decrement('stock', $item['quantity']);

                    // Check stock levels and send notifications if necessary
                    $adminUser = User::where('is_admin', 1)->first();
                    if ($adminUser) {
                        if ($product->stock <= 0) {
                            $adminUser->notify(new OutOfStockNotification($product));
                        } elseif ($product->stock <= 5) { // Threshold for low stock
                            $adminUser->notify(new LowStockNotification($product));
                        }
                    }
                }

                return $order;
            });

            // 4. Try to notify admin (outside the transaction)
            try {
                $adminUser = User::where('is_admin', 1)->first();
                if ($adminUser) {
                    $adminUser->notify(new NewOrderNotification($order));
                }
            } catch (\Exception $e) {
                // Log notification failure but don't fail the order
                Log::error('Failed to send new order notification: ' . $e->getMessage());
            }

            return response()->json(['order' => $order], 201);

        } catch (\Exception $e) {
            // This will catch the stock exception or any other DB errors
            Log::error('Order placement failed: ' . $e->getMessage());
            // Return a more specific error message if it's a stock issue
            if (str_contains($e->getMessage(), 'Not enough stock')) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return response()->json(['message' => 'Order placement failed.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrdersByUser(User $user)
    {
        // Optional: Add authorization check to ensure the authenticated user
        // can only view their own orders, unless they are an admin.
        // if (auth()->id() !== $user->id && !auth()->user()->isAdmin()) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $orders = Order::with('orderItems.product')->where('user_id', $user->id)->latest()->get();
        return response()->json($orders);
    }

    // Update order status
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders')->with('success', 'Order status updated successfully.');
    }



}