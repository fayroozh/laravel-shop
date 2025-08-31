<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
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

        $products = $query->get();
        $categories = Category::all();

        if ($request->expectsJson()) {
            $data = Product::with(['images', 'category'])->get()->map(fn($p) => $this->transformProduct($p));
            return response()->json(['data' => $data, 'categories' => $categories]);
        }

        return view('admin.products', compact('products', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('images', 'category');
        return response()->json(['data' => $this->transformProduct($product)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('--- New Product Store Request ---');
        Log::info('Request data:', $request->except('images'));

        if ($request->hasFile('images')) {
            Log::info('`images` field is present and is a file.');
            $files = $request->file('images');
            if (is_array($files)) {
                Log::info('`images` is an array. Count: ' . count($files));
                foreach ($files as $key => $file) {
                    if ($file->isValid()) {
                        Log::info("File #{$key}:", [
                            'original_name' => $file->getClientOriginalName(),
                            'size' => $file->getSize(),
                        ]);
                    } else {
                        Log::info("File #{$key} is invalid:", [
                            'error' => $file->getError(),
                            'error_message' => $file->getErrorMessage(),
                        ]);
                    }
                }
            } else {
                Log::info('`images` is not an array, it is a single file.');
            }
        } else {
            Log::info('`images` field is NOT present or not a file.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        Log::info('Validation passed.');

        // 1. Create product with main data
        $product = Product::create($request->except('images'));
        Log::info('Product created with ID: ' . $product->id);

        // 2. Handle image uploads
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (!is_array($files)) {
                $files = [$files];
            }

            $firstImagePath = null;

            foreach ($files as $key => $file) {
                Log::info("Processing file #{$key}: {$file->getClientOriginalName()}");
                $path = $file->store('products', 'public');
                Log::info("File stored at path: {$path}");
                $product->images()->create(['image_path' => $path]);
                Log::info("Database record created for image path: {$path}");

                if ($key === 0) {
                    $firstImagePath = $path;
                }
            }

            // 3. Set the main image url
            if ($firstImagePath) {
                $product->image_url = $firstImagePath;
                $product->save();
                Log::info("Main image_url set to: {$firstImagePath}");
            }
        }

        if ($request->wantsJson()) {
            $product->load('images', 'category');
            return response()->json([
                'message' => 'Product created successfully.',
                'data' => $this->transformProduct($product)
            ]);
        }

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // 1. Update main product data
        $product->fill($request->except('images', '_method'));
        $product->save();

        // 2. Handle image uploads (replace existing)
        if ($request->hasFile('images')) {
            // Delete all old images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Upload new images
            $files = $request->file('images');
            if (!is_array($files)) {
                $files = [$files];
            }

            $firstImagePath = null;
            foreach ($files as $key => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create(['image_path' => $path]);

                if ($key === 0) {
                    $firstImagePath = $path;
                }
            }

            // Set the new main image url
            $product->image_url = $firstImagePath;
            $product->save();
        }

        if ($request->wantsJson()) {
            $product->load('images', 'category');
            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => $this->transformProduct($product)
            ]);
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
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
            return response()->json(['message' => 'Product deleted successfully.']);
        }

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    /**
     * API: Product list for the frontend
     * GET /api/frontend/products
     */
    public function apiIndex(Request $request)
    {
        $query = Product::with(['category', 'images']);

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->get();

        $data = $products->map(fn($p) => $this->transformProduct($p));

        return response()->json(['data' => $data]);
    }

    /**
     * API: Single product details for the frontend
     * GET /api/frontend/products/{product}
     */
    public function apiShow(Product $product)
    {
        $product->load(['category', 'images']);
        return response()->json(['data' => $this->transformProduct($product)]);
    }

    /**
     * Transformer to unify product shape and ensure absolute URLs for images
     */
    private function transformProduct(Product $p): array
    {
        // Absolute URL for the main image:
        $mainUrl = $p->image_url
            ? url('storage/' . ltrim($p->image_url, '/'))
            : (optional($p->images->first())->image_path
                ? url('storage/' . $p->images->first()->image_path)
                : null);

        // Image gallery
        $images = $p->images->map(function ($img) {
            $url = !empty($img->url) ? $img->url : url('storage/' . ltrim($img->image_path, '/'));
            return [
                'id' => $img->id,
                'url' => $url,
            ];
        })->values();

        return [
            'id' => $p->id,
            'title' => $p->title,
            'description' => $p->description,
            'price' => $p->price,
            'discount' => $p->discount,
            'rating' => $p->rating,
            'stock' => $p->stock,
            'category_id' => $p->category_id,
            'category' => $p->relationLoaded('category') ? ($p->category?->name) : null,
            'image_url' => $mainUrl,
            'images' => $images,
        ];
    }

}