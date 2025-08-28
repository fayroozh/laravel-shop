<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * عرض كل الموردين
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::all();

        if ($request->wantsJson()) {
            return response()->json($suppliers);
        }

        return view('admin.suppliers', compact('suppliers'));
    }

    /**
     * إضافة مورد جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'company' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ Supplier added successfully',
                'supplier' => $supplier
            ], 201);
        }

        return redirect()->route('admin.suppliers')->with('success', 'Supplier added successfully');
    }

    /**
     * تحديث مورد
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'company' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $supplier->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '✅ Supplier updated successfully',
                'supplier' => $supplier
            ]);
        }

        return redirect()->route('admin.suppliers')->with('success', 'Supplier updated successfully');
    }

    /**
     * حذف مورد
     */
    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => '🗑️ Supplier deleted successfully'
            ]);
        }

        return redirect()->route('admin.suppliers')->with('success', 'Supplier deleted successfully');
    }
}
