<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    private function getUomOptions(): array
    {
        return [
            'Pieces',
            'Kg',
            'Gram',
            'Litre',
            'Millilitre',
            'Meter',
            'Centimeter',
            'Box',
            'Dozen',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::with('customer');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $items = $query->paginate(10);
        $customers = Customer::all();

        return view('items.index', compact('items', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $nextCode = 'CTN' . str_pad(Item::count() + 1, 4, '0', STR_PAD_LEFT);
        $uomOptions = $this->getUomOptions();

        return view('items.create', compact('customers', 'nextCode', 'uomOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'uom' => ['required', 'string', 'max:50', Rule::in($this->getUomOptions())],
            'price' => 'required|numeric|min:0',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load('customer');
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $item->load('customer');
        $customers = Customer::all();
        $uomOptions = $this->getUomOptions();

        return view('items.edit', compact('item', 'customers', 'uomOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'uom' => ['required', 'string', 'max:50', Rule::in($this->getUomOptions())],
            'price' => 'required|numeric|min:0',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function getByCustomer($customerId)
    {
        $items = Item::where('customer_id', $customerId)->get();
        return response()->json($items);
    }
}
