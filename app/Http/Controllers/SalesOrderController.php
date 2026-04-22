<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::with('customer');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('so_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('so_date', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->orderBy('so_date', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $customers = Customer::all();

        return view('sales_orders.index', compact('orders', 'customers'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $so_date = Carbon::today()->format('Y-m-d');
        return view('sales_orders.create', compact('customers', 'so_date'));
    }

    public function store(Request $request)
    {
        try {
            $sanitizeDecimal = function ($value) {
                return (float) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizeInteger = function ($value) {
                return (int) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizedItems = collect($request->input('items', []))->map(function ($item) use ($sanitizeDecimal, $sanitizeInteger) {
                return [
                    'item_id' => $item['item_id'] ?? null,
                    'quantity' => $sanitizeInteger($item['quantity'] ?? 0),
                    'unit_price' => $sanitizeDecimal($item['unit_price'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->toArray();

            $request->merge(['items' => $sanitizedItems]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'so_date' => 'required|date',
                'po_number' => 'nullable|string|max:255',
                'po_date' => 'nullable|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();
            
            $subtotal = 0;
            foreach ($validated['items'] as $itemData) {
                $subtotal += $itemData['quantity'] * $itemData['unit_price'];
            }

            $taxPercent = $request->input('tax_percent', 0);
            $taxAmount = ($subtotal * $taxPercent) / 100;
            $grandTotal = $subtotal + $taxAmount;

            $order = SalesOrder::create([
                'customer_id' => $validated['customer_id'],
                'so_date' => $validated['so_date'],
                'po_number' => $validated['po_number'],
                'po_date' => $validated['po_date'],
                'total_amount' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                    'delivery_date' => $itemData['delivery_date'],
                    'remarks' => $itemData['remarks'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales_orders.index')->with('success', 'Sales Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating Sales Order: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('customer', 'items.item');
        return view('sales_orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.item');
        $customers = Customer::where('status', 'active')->get();
        return view('sales_orders.edit', compact('salesOrder', 'customers'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        try {
            $sanitizeDecimal = function ($value) {
                return (float) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizeInteger = function ($value) {
                return (int) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizedItems = collect($request->input('items', []))->map(function ($item) use ($sanitizeDecimal, $sanitizeInteger) {
                return [
                    'item_id' => $item['item_id'] ?? null,
                    'quantity' => $sanitizeInteger($item['quantity'] ?? 0),
                    'unit_price' => $sanitizeDecimal($item['unit_price'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->toArray();

            $request->merge(['items' => $sanitizedItems]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'so_date' => 'required|date',
                'po_number' => 'nullable|string|max:255',
                'po_date' => 'nullable|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $subtotal = 0;
            foreach ($validated['items'] as $itemData) {
                $subtotal += $itemData['quantity'] * $itemData['unit_price'];
            }

            $taxPercent = $request->input('tax_percent', 0);
            $taxAmount = ($subtotal * $taxPercent) / 100;
            $grandTotal = $subtotal + $taxAmount;

            $salesOrder->update([
                'customer_id' => $validated['customer_id'],
                'so_date' => $validated['so_date'],
                'po_number' => $validated['po_number'],
                'po_date' => $validated['po_date'],
                'total_amount' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $salesOrder->items()->delete();

            foreach ($validated['items'] as $itemData) {
                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                    'delivery_date' => $itemData['delivery_date'],
                    'remarks' => $itemData['remarks'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales_orders.index')->with('success', 'Sales Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating Sales Order: ' . $e->getMessage());
        }
    }

    public function destroy(SalesOrder $salesOrder)
    {
        try {
            DB::beginTransaction();
            $salesOrder->items()->delete();
            $salesOrder->delete();
            DB::commit();
            return redirect()->route('sales_orders.index')->with('success', 'Sales Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales_orders.index')->with('error', 'Error deleting Sales Order: ' . $e->getMessage());
        }
    }
}
