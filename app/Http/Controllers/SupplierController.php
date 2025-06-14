<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index() {
    return Supplier::all();
}


public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'company' => 'nullable|string',
        'email' => 'nullable|email',
        'phone' => 'nullable|string'
    ]);

    \App\Models\Supplier::create($data);

    return redirect()->route('admin.suppliers')->with('success', 'Supplier added successfully');
}

public function update(Request $request, Supplier $supplier) {
    $data = $request->validate([
        'name' => 'required|string',
        'company' => 'nullable|string',
        'email' => 'nullable|email',
        'phone' => 'nullable|string',
        'address' => 'nullable|string'
    ]);
    $supplier->update($data);
    return redirect()->route('admin.suppliers')->with('success', 'Supplier updated successfully');
}

public function destroy(Supplier $supplier) {
    $supplier->delete();
    return redirect()->route('admin.suppliers')->with('success', 'Supplier deleted successfully');
}

}
