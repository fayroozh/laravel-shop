<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // جلب كل المنتجات مع التصنيفات
    public function index(Request $request)
    {
        $query = Product::with('category'); // تحميل التصنيفات مع المنتجات

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
            $sortBy = $request->sort;
            $query->orderBy($sortBy, 'asc');
        }

        $products = $query->get();
        
        return response()->json([
            'products' => $products,
            'total' => $products->count()
        ]);
    }

    // إضافة منتج جديد وتسجيل المخزون
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

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        // تسجيل حركة المخزون الأولية
        if ($product->stock > 0) {
            $product->updateStock(
                $product->stock,
                'in',
                'initial',
                null,
                'Initial stock when product was created'
            );
        }

        if ($request->expectsJson()) {
            return response()->json($product, 201);
        }

        return redirect()->route('admin.products')->with('success', 'Product added successfully');
    }

    // عرض منتج محدد
    public function show(Product $product)
    {
        return $product;
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
            'image_url' => 'nullable|string'
        ]);

        $product->update($data);
        return response()->json($product);
    }

    // حذف منتج
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    // تعديل المخزون يدوياً
    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|not_in:0',
            'notes' => 'nullable|string'
        ]);

        $type = $validated['quantity'] > 0 ? 'in' : 'out';

        $product->updateStock(
            $validated['quantity'],
            $type,
            'manual',
            null,
            $validated['notes']
        );

        return redirect()->back()->with('success', 'تم تعديل المخزون بنجاح');
    }
}
