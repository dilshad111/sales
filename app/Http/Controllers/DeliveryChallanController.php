<?php

namespace App\Http\Controllers;

use App\Models\DeliveryChallan;
use App\Models\DeliveryChallanItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillExpense;
use App\Models\Customer;
use App\Models\Item;
use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryChallanController extends Controller
{
    /**
     * Display a listing of delivery challans.
     */
    public function index(Request $request)
    {
        $query = DeliveryChallan::with('customer', 'bill');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('challan_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('challan_date', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 20);
        $challans = $query->orderBy('challan_date', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $customers = Customer::all();

        return view('delivery_challans.index', compact('challans', 'customers'));
    }

    /**
     * Show the form for creating a new delivery challan.
     */
    public function create(Request $request)
    {
        $salesOrder = null;
        if ($request->has('sales_order_id')) {
            $salesOrder = SalesOrder::with('customer', 'items.item')->find($request->sales_order_id);
        }

        $customers = Customer::where('status', 'active')
            ->where('type', 'Un-Official')
            ->get();
            
        $setting = \App\Models\CompanySetting::first();
        $prefix = $setting ? ($setting->challan_prefix ?? 'DC') : 'DC';
        
        $lastChallan = DeliveryChallan::where('challan_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($lastChallan) {
            $lastNumStr = str_replace($prefix, '', $lastChallan->challan_number);
            $nextNumber = (int) $lastNumStr + 1;
        }
        $nextChallanNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('delivery_challans.create', compact('customers', 'nextChallanNumber', 'salesOrder'));
    }

    /**
     * Show selection screen for Sales Orders.
     */
    public function selectSO(Request $request)
    {
        $query = SalesOrder::with('customer')
            ->where('status', 'pending');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('so_number', 'like', "%$search%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%$search%");
                  });
            });
        }

        $salesOrders = $query->latest()->paginate(20);
        return view('delivery_challans.select_so', compact('salesOrders'));
    }

    /**
     * Store a newly created delivery challan.
     */
    public function store(Request $request)
    {
        try {
            $sanitizeDecimal = function ($value) {
                return (float) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizeInteger = function ($value) {
                return (int) str_replace(',', '', (string) ($value ?? 0));
            };

            $formatDeliveryDate = function ($value) {
                if (empty($value)) return null;
                try {
                    if (str_contains($value, '/')) {
                        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                    }
                    return Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            $sanitizedItems = collect($request->input('items', []))->map(function ($item) use ($sanitizeDecimal, $sanitizeInteger) {
                return [
                    'item_id' => $item['item_id'] ?? null,
                    'sales_order_item_id' => $item['sales_order_item_id'] ?? null,
                    'quantity' => $sanitizeInteger($item['quantity'] ?? 0),
                    'bundles' => $item['bundles'] ?? null,
                    'rate' => $sanitizeDecimal($item['rate'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->map(function ($item) use ($formatDeliveryDate) {
                $item['delivery_date'] = $formatDeliveryDate($item['delivery_date']);
                return $item;
            })->toArray();

            $request->merge(['items' => $sanitizedItems]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'sales_order_id' => 'nullable|exists:sales_orders,id',
                'challan_date' => 'required|date|before_or_equal:today',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.sales_order_item_id' => 'nullable|exists:sales_order_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.bundles' => 'nullable|string|max:255',
                'items.*.rate' => 'nullable|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:500',
                'vehicle_number' => 'nullable|string|max:255',
            ]);

            $customer = Customer::find($validated['customer_id']);
            $excessPercent = (float) ($customer->excess_qty_percent ?? 0);

            DB::beginTransaction();
            try {
                $subtotal = 0;

                $challan = DeliveryChallan::create([
                    'customer_id' => $validated['customer_id'],
                    'sales_order_id' => $validated['sales_order_id'] ?? null,
                    'challan_date' => $validated['challan_date'],
                    'status' => 'pending',
                    'remarks' => $validated['remarks'] ?? null,
                    'vehicle_number' => $validated['vehicle_number'] ?? null,
                ]);

                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    
                    if ($itemData['sales_order_item_id']) {
                        $soItem = SalesOrderItem::find($itemData['sales_order_item_id']);
                        if ($soItem) {
                            $deliveredSoFar = $soItem->delivered_quantity;
                            $maxTotalAllowed = $soItem->quantity * (1 + ($excessPercent / 100));
                            $remainingAllowed = $maxTotalAllowed - $deliveredSoFar;

                            if ($itemData['quantity'] > ($remainingAllowed + 0.01)) { // Added small margin for float comparison
                                throw new \Exception("Quantity for item " . $item->name . " exceeds the remaining allowed limit. (Remaining: " . floor($remainingAllowed) . ")");
                            }
                        }
                    }

                    $rate = (float) ($item->price ?? 0);
                    $quantity = (int) $itemData['quantity'];
                    $lineTotal = $rate * $quantity;
                    $subtotal += $lineTotal;

                    DeliveryChallanItem::create([
                        'delivery_challan_id' => $challan->id,
                        'item_id' => $itemData['item_id'],
                        'sales_order_item_id' => $itemData['sales_order_item_id'] ?? null,
                        'quantity' => $quantity,
                        'bundles' => $itemData['bundles'] ?? null,
                        'price' => $rate,
                        'total' => $lineTotal,
                        'delivery_date' => $itemData['delivery_date'] ?? null,
                        'remarks' => $itemData['remarks'] ?? null,
                    ]);
                }

                $challan->update(['total' => $subtotal]);

                DB::commit();
                return redirect()->route('delivery_challans.index')->with('success', 'Delivery Challan created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error creating challan: ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified delivery challan.
     */
    public function show(DeliveryChallan $deliveryChallan)
    {
        $deliveryChallan->load('customer', 'items.item', 'bill');

        return view('delivery_challans.show', compact('deliveryChallan'));
    }

    /**
     * Show the form for editing the specified delivery challan.
     */
    public function edit(DeliveryChallan $deliveryChallan)
    {
        if ($deliveryChallan->status === 'billed') {
            return redirect()->route('delivery_challans.show', $deliveryChallan)
                ->with('error', 'Cannot edit a challan that has already been billed.');
        }

        $deliveryChallan->load('items.item');

        $customers = Customer::where('status', 'active')
            ->where('type', 'Un-Official')
            ->get();

        $itemsForCustomer = collect();
        if ($deliveryChallan->customer_id) {
            $itemsForCustomer = Item::where('customer_id', $deliveryChallan->customer_id)
                ->orderBy('name')
                ->get();

            $additionalItems = $deliveryChallan->items
                ->map->item
                ->filter()
                ->reject(function ($item) use ($deliveryChallan) {
                    return $item->customer_id && $item->customer_id !== $deliveryChallan->customer_id;
                });

            $itemsForCustomer = $itemsForCustomer->merge($additionalItems)->unique('id')->sortBy('name')->values();
        }

        return view('delivery_challans.edit', compact('deliveryChallan', 'customers', 'itemsForCustomer'));
    }

    /**
     * Update the specified delivery challan.
     */
    public function update(Request $request, DeliveryChallan $deliveryChallan)
    {
        if ($deliveryChallan->status === 'billed') {
            return back()->with('error', 'Cannot update a challan that has been billed.');
        }

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
                    'bundles' => $item['bundles'] ?? null,
                    'rate' => $sanitizeDecimal($item['rate'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->toArray();

            $request->merge(['items' => $sanitizedItems]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'challan_date' => 'required|date|before_or_equal:today',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.bundles' => 'nullable|string|max:255',
                'items.*.rate' => 'nullable|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:500',
                'vehicle_number' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            try {
                $deliveryChallan->update([
                    'customer_id' => $validated['customer_id'],
                    'challan_date' => $validated['challan_date'],
                    'remarks' => $validated['remarks'] ?? null,
                    'vehicle_number' => $validated['vehicle_number'] ?? null,
                ]);

                $deliveryChallan->items()->delete();

                $subtotal = 0;
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    $rate = (float) ($item->price ?? 0);
                    $quantity = (int) $itemData['quantity'];
                    $lineTotal = $rate * $quantity;
                    $subtotal += $lineTotal;

                    DeliveryChallanItem::create([
                        'delivery_challan_id' => $deliveryChallan->id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $quantity,
                        'bundles' => $itemData['bundles'] ?? null,
                        'price' => $rate,
                        'total' => $lineTotal,
                        'delivery_date' => $itemData['delivery_date'] ?? null,
                        'remarks' => $itemData['remarks'] ?? null,
                    ]);
                }

                $deliveryChallan->update(['total' => $subtotal]);

                DB::commit();
                return redirect()->route('delivery_challans.show', $deliveryChallan)->with('success', 'Delivery Challan updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Error updating challan: ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified delivery challan.
     */
    public function destroy(DeliveryChallan $deliveryChallan)
    {
        if ($deliveryChallan->status === 'billed') {
            return redirect()->route('delivery_challans.index')
                ->with('error', 'Cannot delete a challan that has already been billed.');
        }

        try {
            DB::beginTransaction();
            $deliveryChallan->items()->delete();
            $deliveryChallan->delete();
            DB::commit();
            return redirect()->route('delivery_challans.index')->with('success', 'Delivery Challan deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('delivery_challans.index')->with('error', 'Error deleting challan: ' . $e->getMessage());
        }
    }

    /**
     * Print the delivery challan.
     */
    public function print(DeliveryChallan $deliveryChallan)
    {
        $deliveryChallan->load('customer', 'items.item');

        return view('delivery_challans.print', compact('deliveryChallan'));
    }

    /**
     * Create a bill from selected delivery challans.
     */
    public function createBill(Request $request)
    {
        $request->validate([
            'challan_ids' => 'required|array|min:1',
            'challan_ids.*' => 'exists:delivery_challans,id',
        ]);

        $challans = DeliveryChallan::with('items.item', 'customer')
            ->whereIn('id', $request->challan_ids)
            ->where('status', 'pending')
            ->get();

        if ($challans->isEmpty()) {
            return back()->with('error', 'No pending challans found for the selected items.');
        }

        // Verify all challans belong to the same customer
        $customerIds = $challans->pluck('customer_id')->unique();
        if ($customerIds->count() > 1) {
            return back()->with('error', 'All selected challans must belong to the same customer.');
        }

        DB::beginTransaction();
        try {
            $customerId = $customerIds->first();
            $subtotal = 0;

            $bill = Bill::create([
                'customer_id' => $customerId,
                'bill_date' => Carbon::today(),
                'total' => 0,
                'discount' => 0,
                'tax' => 0,
            ]);

            foreach ($challans as $challan) {
                foreach ($challan->items as $challanItem) {
                    $lineTotal = $challanItem->price * $challanItem->quantity;
                    $subtotal += $lineTotal;

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'item_id' => $challanItem->item_id,
                        'quantity' => $challanItem->quantity,
                        'price' => $challanItem->price,
                        'total' => $lineTotal,
                        'delivery_date' => $challanItem->delivery_date,
                        'remarks' => $challanItem->remarks ? $challanItem->remarks . ' (DC: ' . $challan->challan_number . ')' : 'DC: ' . $challan->challan_number,
                    ]);
                }

                $challan->update([
                    'status' => 'billed',
                    'bill_id' => $bill->id,
                ]);
            }

            $bill->update(['total' => $subtotal]);

            DB::commit();
            return redirect()->route('bills.edit', $bill)->with('success', 'Bill created from ' . $challans->count() . ' delivery challan(s). You can now add discount, tax and expenses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating bill from challans: ' . $e->getMessage());
        }
    }
}
