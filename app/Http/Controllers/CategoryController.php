<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // عرض كل التصنيفات للـ Admin Panel
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    // إضافة تصنيف جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories')->with('success', 'Category added successfully!');
    }

    // عرض تصنيف معين
    public function show(Category $category)
    {
        return $category;
    }

    // تعديل تصنيف
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }

    // حذف تصنيف
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    // ========================
    // API Methods for Frontend
    // ========================

    // جميع التصنيفات كـ JSON
    public function apiIndex()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // عرض تصنيف معين كـ JSON
    public function apiShow(Category $category)
    {
        return response()->json($category);
    }

    // إنشاء تصنيف عبر API
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully!',
            'category' => $category
        ], 201);
    }

    // تعديل تصنيف عبر API
    public function apiUpdate(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully!',
            'category' => $category
        ]);
    }

    // حذف تصنيف عبر API
    public function apiDestroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully!'
        ]);
    }
}
