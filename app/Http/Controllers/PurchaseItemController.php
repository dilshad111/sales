<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public function index()
    {
        $items = PurchaseItem::with('supplier')->orderBy('name')->paginate(20);
        return view('purchase_items.index', compact('items'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('purchase_items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        PurchaseItem::create($request->all());

        return redirect()->route('purchase_items.index')->with('success', 'Purchase item created successfully.');
    }

    public function edit(PurchaseItem $purchaseItem)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        return view('purchase_items.edit', compact('purchaseItem', 'suppliers'));
    }

    public function update(Request $request, PurchaseItem $purchaseItem)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $purchaseItem->update($request->all());

        return redirect()->route('purchase_items.index')->with('success', 'Purchase item updated successfully.');
    }

    public function getBySupplier($supplier_id)
    {
        $items = PurchaseItem::where('supplier_id', $supplier_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        return response()->json($items);
    }

    public function destroy(PurchaseItem $purchaseItem)
    {
        $purchaseItem->delete();
        return redirect()->route('purchase_items.index')->with('success', 'Purchase item deleted successfully.');
    }
}
