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
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $paths = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('product_images', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path
            ]);
            $paths[] = $path;
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'paths' => $paths
        ], 201);
    }
}

