<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $images = $request->file('images');
        if (!is_array($images)) {
            $images = [$images];
        }

        $paths = [];

        foreach ($images as $image) {
            $path = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path
            ]);
            $paths[] = $path;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Images uploaded successfully',
                'paths' => $paths
            ], 201);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully');
    }

    public function destroy(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Image deleted successfully']);
        }
        return redirect()->back()->with('success', 'Image deleted successfully');
    }

}