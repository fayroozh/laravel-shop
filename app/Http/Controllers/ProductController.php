<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * API: Fetch all products for the React frontend.
     */
    public function apiIndex()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    /**
     * Display a listing of the resource.
     */
    // جلب كل المنتجات للعرض
    public function index(Request $request)
    {
        $query = Product::with('category'); // تحميل التصنيف فقط

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('sort')) {
            $query->orderBy($request->sort, 'asc');
        }

        $products = $query->get();
        $categories = Category::all();

        // عرض Blade في admin/products.blade.php
        return view('admin.products', compact('products', 'categories'));
    }

    // صفحة إنشاء منتج جديد
    public function create()
    {
        $categories = Category::all();
        return view('admin.products', compact('categories'));
    }

    // إضافة منتج جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'rating' => 'nullable|numeric',
            'stock' => 'nullable|integer',
        ]);

        Product::create($validated);

        return redirect()->route('admin.products')->with('success', 'تمت إضافة المنتج بنجاح');
    }

    // عرض صفحة تعديل منتج
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products', compact('product', 'categories'));
    }

    // تحديث منتج
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'تم تحديث المنتج بنجاح');
    }

    // حذف منتج
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'تم حذف المنتج');
    }
}