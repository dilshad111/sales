<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDeliveryChallan;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseDeliveryChallanController extends Controller
{
    public function index()
    {
        $challans = PurchaseDeliveryChallan::with(['supplier'])
            ->orderBy('date', 'desc')
            ->paginate(15);
        return view('purchase_delivery_challans.index', compact('challans'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $items = PurchaseItem::where('status', 'active')->orderBy('name')->get();
        
        // Generate a simple unique DC number
        $nextId = PurchaseDeliveryChallan::max('id') + 1;
        $challanNumber = 'PDC-' . date('ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('purchase_delivery_challans.create', compact('suppliers', 'items', 'challanNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'challan_number' => 'required|unique:purchase_delivery_challans,challan_number',
            'supplier_dc_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'vehicle_number' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $taxPercent = $item['tax_percent'] ?? 0;
                $amount = $item['quantity'] * $item['rate'];
                $taxAmount = ($amount * $taxPercent) / 100;
                $totalAmount += ($amount + $taxAmount);
            }

            $challan = PurchaseDeliveryChallan::create([
                'challan_number' => $request->challan_number,
                'supplier_dc_number' => $request->supplier_dc_number,
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'vehicle_number' => $request->vehicle_number,
                'total_amount' => $totalAmount,
                'remarks' => $request->remarks,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $purchaseItem = PurchaseItem::find($item['purchase_item_id']);
                $taxPercent = $item['tax_percent'] ?? 0;
                $amount = $item['quantity'] * $item['rate'];
                $taxAmount = ($amount * $taxPercent) / 100;
                $lineTotal = $amount + $taxAmount;

                $challan->items()->create([
                    'purchase_item_id' => $item['purchase_item_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $purchaseItem->unit,
                    'rate' => $item['rate'],
                    'amount' => $amount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $lineTotal,
                ]);
            }

            return redirect()->route('purchase_delivery_challans.index')->with('success', 'Purchase Delivery Challan created successfully.');
        });
    }

    public function show(PurchaseDeliveryChallan $purchase_delivery_challan)
    {
        $purchase_delivery_challan->load(['supplier', 'items.item']);
        return view('purchase_delivery_challans.show', compact('purchase_delivery_challan'));
    }

    public function edit(PurchaseDeliveryChallan $purchase_delivery_challan)
    {
        $purchase_delivery_challan->load('items');
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        
        // Get items linked to the current supplier
        $items = PurchaseItem::where('supplier_id', $purchase_delivery_challan->supplier_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('purchase_delivery_challans.edit', compact('purchase_delivery_challan', 'suppliers', 'items'));
    }

    public function update(Request $request, PurchaseDeliveryChallan $purchase_delivery_challan)
    {
        $request->validate([
            'supplier_dc_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'vehicle_number' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($request, $purchase_delivery_challan) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $taxPercent = $item['tax_percent'] ?? 0;
                $amount = $item['quantity'] * $item['rate'];
                $taxAmount = ($amount * $taxPercent) / 100;
                $totalAmount += ($amount + $taxAmount);
            }

            $purchase_delivery_challan->update([
                'supplier_dc_number' => $request->supplier_dc_number,
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'vehicle_number' => $request->vehicle_number,
                'total_amount' => $totalAmount,
                'remarks' => $request->remarks,
            ]);

            // Re-sync items
            $purchase_delivery_challan->items()->delete();

            foreach ($request->items as $item) {
                $purchaseItem = PurchaseItem::find($item['purchase_item_id']);
                $taxPercent = $item['tax_percent'] ?? 0;
                $amount = $item['quantity'] * $item['rate'];
                $taxAmount = ($amount * $taxPercent) / 100;
                $lineTotal = $amount + $taxAmount;

                $purchase_delivery_challan->items()->create([
                    'purchase_item_id' => $item['purchase_item_id'],
                    'quantity' => $item['quantity'],
                    'unit' => $purchaseItem->unit,
                    'rate' => $item['rate'],
                    'amount' => $amount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $lineTotal,
                ]);
            }

            return redirect()->route('purchase_delivery_challans.index')->with('success', 'Purchase Delivery Challan updated successfully.');
        });
    }

    public function destroy(PurchaseDeliveryChallan $purchase_delivery_challan)
    {
        $purchase_delivery_challan->delete();
        return redirect()->route('purchase_delivery_challans.index')->with('success', 'Purchase Delivery Challan deleted successfully.');
    }
}
