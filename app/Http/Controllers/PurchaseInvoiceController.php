<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $invoices = PurchaseInvoice::with(['supplier', 'agent'])
            ->orderBy('date', 'desc')
            ->paginate(15);
        return view('purchase_invoices.index', compact('invoices'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $agents = Agent::where('status', 'active')->orderBy('name')->get();
        $items = PurchaseItem::where('status', 'active')->orderBy('name')->get();
        
        // Generate a simple unique invoice number
        $nextId = PurchaseInvoice::max('id') + 1;
        $invoiceNumber = 'PINV-' . date('ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('purchase_invoices.create', compact('suppliers', 'agents', 'items', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:purchase_invoices,invoice_number',
            'supplier_invoice_number' => 'nullable|string|max:255',
            'date' => 'required|date',
            'posting_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'agent_id' => 'nullable|exists:agents,id',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $totalGross = 0;
            foreach ($request->items as $item) {
                $totalGross += ($item['quantity'] * $item['unit_price']);
            }

            // Calculate tax amount from percentage
            $taxAmount = ($totalGross * $request->tax_percentage) / 100;
            
            // DO NOT deduct commission here as per user request
            $commissionAmount = ($totalGross * $request->commission_percentage) / 100;
            $netAmount = $totalGross + $taxAmount; // Net is Gross + Tax Amt

            $invoice = PurchaseInvoice::create([
                'invoice_number' => $request->invoice_number,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'date' => $request->date,
                'posting_date' => $request->posting_date,
                'supplier_id' => $request->supplier_id,
                'agent_id' => $request->agent_id,
                'commission_percentage' => $request->commission_percentage,
                'gross_amount' => $totalGross,
                'tax_percentage' => $request->tax_percentage,
                'tax_amount' => $taxAmount,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'purchase_item_id' => $item['purchase_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            return redirect()->route('purchase_invoices.index')->with('success', 'Purchase Invoice created successfully.');
        });
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'agent', 'items.item', 'recoveries']);
        return view('purchase_invoices.show', compact('purchaseInvoice'));
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->delete();
        return redirect()->route('purchase_invoices.index')->with('success', 'Purchase Invoice deleted successfully.');
    }
}
