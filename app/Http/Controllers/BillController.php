<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bill::with('customer');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }

        $bills = $query->paginate(10);
        $customers = Customer::all();

        return view('bills.index', compact('bills', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $nextBillNumber = 'BILL' . str_pad(Bill::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('bills.create', compact('customers', 'nextBillNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Bill creation request received', ['request_data' => $request->all()]);
        
        try {
            $sanitizeDecimal = function ($value) {
                return (float) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizeInteger = function ($value) {
                return (int) str_replace(',', '', (string) ($value ?? 0));
            };

            $formatDeliveryDate = function ($value) {
                if (empty($value)) {
                    return null;
                }

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
                    'quantity' => $sanitizeInteger($item['quantity'] ?? 0),
                    'rate' => $sanitizeDecimal($item['rate'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->map(function ($item) use ($formatDeliveryDate) {
                $item['delivery_date'] = $formatDeliveryDate($item['delivery_date']);
                return $item;
            })->toArray();

            $request->merge([
                'discount' => $sanitizeDecimal($request->discount ?? 0),
                'tax' => $sanitizeDecimal($request->tax ?? 0),
                'items' => $sanitizedItems,
            ]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'bill_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'discount' => 'nullable|numeric',
                'tax' => 'nullable|numeric',
            ]);
            
            \Log::info('Validation passed', ['validated_data' => $validated]);

            DB::beginTransaction();
            try {
            $subtotal = 0;

            $discount = $validated['discount'] ?? 0;
            $tax = $validated['tax'] ?? 0;

            $bill = Bill::create([
                'customer_id' => $validated['customer_id'],
                'bill_date' => $validated['bill_date'],
                'total' => 0, // temporary, update after calculating items
                'discount' => $discount,
                'tax' => $tax,
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = Item::find($itemData['item_id']);

                $quantity = (int) $itemData['quantity'];
                $rate = (float) $itemData['rate'];
                $lineTotal = $rate * $quantity;

                $subtotal += $lineTotal;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $quantity,
                    'price' => $rate,
                    'total' => $lineTotal,
                    'delivery_date' => $itemData['delivery_date'] ?? null,
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

                $finalTotal = $subtotal - $discount + $tax;

                $bill->update([
                    'total' => $finalTotal,
                ]);
                
                \DB::commit();
                \Log::info('Bill created successfully', ['bill_id' => $bill->id]);
                
                return redirect()->route('bills.index')->with('success', 'Bill created successfully.');
                
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Error creating bill: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Return back with error message
                return back()->withInput()->with('error', 'Error creating bill: ' . $e->getMessage());
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Unexpected error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        $bill->load('customer', 'billItems.item');

        return view('bills.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        $bill->load('billItems.item');

        $customers = Customer::all();

        $itemsForCustomer = collect();
        if ($bill->customer_id) {
            $itemsForCustomer = Item::where('customer_id', $bill->customer_id)
                ->orderBy('name')
                ->get();

            $additionalItems = $bill->billItems
                ->map->item
                ->filter()
                ->reject(function ($item) use ($bill) {
                    return $item->customer_id && $item->customer_id !== $bill->customer_id;
                });

            $itemsForCustomer = $itemsForCustomer->merge($additionalItems)->unique('id')->sortBy('name')->values();
        }

        return view('bills.edit', compact('bill', 'customers', 'itemsForCustomer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bill $bill)
    {
        \Log::info('Bill update request received', ['bill_id' => $bill->id, 'request_data' => $request->all()]);
        
        try {
            $sanitizeDecimal = function ($value) {
                return (float) str_replace(',', '', (string) ($value ?? 0));
            };

            $sanitizeInteger = function ($value) {
                return (int) str_replace(',', '', (string) ($value ?? 0));
            };

            // Sanitize items before validation
            $sanitizedItems = collect($request->input('items', []))->map(function ($item) use ($sanitizeDecimal, $sanitizeInteger) {
                return [
                    'item_id' => $item['item_id'] ?? null,
                    'quantity' => $sanitizeInteger($item['quantity'] ?? 0),
                    'rate' => $sanitizeDecimal($item['rate'] ?? 0),
                    'delivery_date' => $item['delivery_date'] ?? null,
                    'remarks' => $item['remarks'] ?? null,
                ];
            })->toArray();

            $request->merge([
                'discount' => $sanitizeDecimal($request->discount ?? 0),
                'tax' => $sanitizeDecimal($request->tax ?? 0),
                'items' => $sanitizedItems,
            ]);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'bill_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.delivery_date' => 'nullable|date',
                'items.*.remarks' => 'nullable|string|max:255',
                'discount' => 'nullable|numeric',
                'tax' => 'nullable|numeric',
            ]);
            
            \Log::info('Validation passed', ['validated_data' => $validated]);

            DB::beginTransaction();
            try {
                $discount = $validated['discount'] ?? 0;
                $tax = $validated['tax'] ?? 0;

                $bill->update([
                    'customer_id' => $validated['customer_id'],
                    'bill_date' => $validated['bill_date'],
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => 0,
                ]);

                // Delete existing bill items
                $bill->billItems()->delete();

                $subtotal = 0;

                foreach ($validated['items'] as $itemData) {
                    $quantity = (int) $itemData['quantity'];
                    $rate = (float) $itemData['rate'];
                    $lineTotal = $rate * $quantity;

                    $subtotal += $lineTotal;

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $quantity,
                        'price' => $rate,
                        'total' => $lineTotal,
                        'delivery_date' => $itemData['delivery_date'] ?? null,
                        'remarks' => $itemData['remarks'] ?? null,
                    ]);
                }

                $finalTotal = $subtotal - $discount + $tax;

                $bill->update([
                    'total' => $finalTotal,
                ]);
                
                DB::commit();
                \Log::info('Bill updated successfully', ['bill_id' => $bill->id]);

                return redirect()->route('bills.show', $bill)->with('success', 'Bill updated successfully.');
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating bill: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return back()->withInput()->with('error', 'Error updating bill: ' . $e->getMessage());
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Unexpected error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->route('bills.index')->with('success', 'Bill deleted successfully.');
    }

    public function downloadPdf(Bill $bill)
    {
        $bill->load('customer', 'billItems.item');

        $pdf = Pdf::loadView('bills.pdf', compact('bill'))
        ->setPaper('a4', 'portrait');

        return $pdf->download('bill_' . $bill->id . '.pdf');
    }

    public function print(Bill $bill)
    {
        $bill->load('customer', 'billItems.item');

        return view('bills.print', compact('bill'));
    }
}
