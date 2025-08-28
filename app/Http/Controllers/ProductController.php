<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * عرض كل المنتجات للـ Blade و JSON للـ API (لو طُلب JSON من نفس الصفحة)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

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

        $products   = $query->get();
        $categories = Category::all();

        if ($request->expectsJson()) {
            $data = Product::with(['images','category'])->get()->map(fn($p) => $this->transformProduct($p));
            return response()->json(['data' => $data, 'categories' => $categories], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return view('admin.products', compact('products', 'categories'));
    }

    /**
     * عرض منتج واحد (للإدارة/الداخلي)
     */
    public function show(Product $product)
    {
        $product->load('images', 'category');
        return response()->json(['data' => $this->transformProduct($product)], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * صفحة إنشاء منتج (Blade فقط)
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products', compact('categories'));
    }

    /**
     * تخزين منتج جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric',
            'discount'    => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'stock'       => 'nullable|integer',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // الصورة الرئيسية = أول عنصر في المصفوفة
        if ($request->hasFile('images')) {
            $path = $request->file('images')[0]->store('products', 'public');
            $validated['image_url'] = $path; // نخزن المسار النسبي (products/..)
        }

        $product = Product::create($validated);

        // بقية الصور كمعرض
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            for ($i = 1; $i < count($files); $i++) {
                $path = $files[$i]->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ تمت إضافة المنتج بنجاح',
                'data'    => $this->transformProduct($product->load(['images','category']))
            ], 201, [], JSON_UNESCAPED_UNICODE);
        }

        return redirect()->route('admin.products')->with('success', '✅ تمت إضافة المنتج بنجاح');
    }

    /**
     * صفحة تعديل منتج (Blade فقط)
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products', compact('product', 'categories'));
    }

    /**
     * تحديث منتج
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title'       => 'sometimes|string',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric',
            'stock'       => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasFile('images')) {
            $files = $request->file('images');

            // حدّث الصورة الرئيسية
            if (isset($files[0])) {
                if ($product->image_url) {
                    Storage::disk('public')->delete($product->image_url);
                }
                $path = $files[0]->store('products', 'public');
                $data['image_url'] = $path;
            }

            // استبدال معرض الصور كاملاً (تبسيطًا)
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
            for ($i = 1; $i < count($files); $i++) {
                $path = $files[$i]->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        $product->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ تم تحديث المنتج بنجاح',
                'data'    => $this->transformProduct($product->load(['images','category']))
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return redirect()->route('admin.products')->with('success', '✅ تم تحديث المنتج بنجاح');
    }

    /**
     * حذف منتج
     */
    public function destroy(Request $request, Product $product)
    {
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        $product->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => '🗑 تم حذف المنتج بنجاح'], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return redirect()->route('admin.products')->with('success', '🗑 تم حذف المنتج بنجاح');
    }

    /**
     * API: قائمة المنتجات للواجهة
     * GET /api/frontend/products
     */
    public function apiIndex(Request $request)
    {
        $products = Product::with(['category','images'])
            ->when($request->has('keyword'), fn($q) => $q->where('title', 'like', '%' . $request->keyword . '%'))
            ->when($request->has('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->has('min_price'), fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->has('max_price'), fn($q) => $q->where('price', '<=', $request->max_price))
            ->get();

        $data = $products->map(fn($p) => $this->transformProduct($p));

        return response()->json(['data' => $data], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * API: تفاصيل منتج واحد للواجهة
     * GET /api/frontend/products/{product}
     */
    public function apiShow(Product $product)
    {
        $product->load(['category','images']);
        return response()->json(['data' => $this->transformProduct($product)], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * محوّل يوحّد شكل المنتج ويضمن URL مطلقة للصور
     */
    private function transformProduct(Product $p): array
    {
        // URL مطلق للصورة الرئيسية:
        $mainUrl = $p->image_url
            ? url('storage/' . ltrim($p->image_url, '/'))  // products/.. -> http://.../storage/products/..
            : (optional($p->images->first())->image_path
                ? url('storage/' . $p->images->first()->image_path)
                : null);

        // معرض الصور
        $images = $p->images->map(function ($img) {
            $url = !empty($img->url) ? $img->url : url('storage/' . ltrim($img->image_path, '/'));
            return [
                'id'  => $img->id,
                'url' => $url,
            ];
        })->values();

        return [
            'id'          => $p->id,
            'title'       => $p->title,
            'description' => $p->description,
            'price'       => $p->price,
            'discount'    => $p->discount,
            'rating'      => $p->rating,
            'stock'       => $p->stock,
            'category_id' => $p->category_id,
            'category'    => $p->relationLoaded('category') ? ($p->category?->name) : null,
            'image_url'   => $mainUrl,
            'images'      => $images,
        ];
    }
}