<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PurchaseInvoice;
use App\Models\Recovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecoveryController extends Controller
{
    public function index()
    {
        $recoveries = Recovery::with(['invoice.supplier', 'agent', 'directorAccount'])
            ->orderBy('date', 'desc')
            ->paginate(15);
        return view('recoveries.index', compact('recoveries'));
    }

    public function create()
    {
        // Only show invoices that are not fully recovered
        $invoices = PurchaseInvoice::where('status', '!=', 'recovered')
            ->with(['supplier', 'agent'])
            ->orderBy('date', 'desc')
            ->get();
            
        $directorAccounts = Account::where('type', 'payment_party')
            ->orderBy('name')
            ->get();
            
        // Generate recovery number
        $nextId = Recovery::max('id') + 1;
        $recoveryNumber = 'REC-' . date('ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('recoveries.create', compact('invoices', 'directorAccounts', 'recoveryNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recovery_number' => 'required|unique:recoveries,recovery_number',
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'director_account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
        ]);

        return DB::transaction(function () use ($request) {
            $invoice = PurchaseInvoice::findOrFail($request->purchase_invoice_id);
            
            // Calculate commission portion of this recovery
            // Recovery Amount is usually the Full amount collected. 
            // We deduct commission from it to get net profit for director.
            $commissionDeducted = ($request->amount * $invoice->commission_percentage) / 100;
            $netTransfer = $request->amount - $commissionDeducted;

            $recovery = Recovery::create([
                'recovery_number' => $request->recovery_number,
                'purchase_invoice_id' => $request->purchase_invoice_id,
                'agent_id' => $invoice->agent_id,
                'amount' => $request->amount,
                'commission_deducted' => $commissionDeducted,
                'net_amount_transfered' => $netTransfer,
                'director_account_id' => $request->director_account_id,
                'date' => $request->date,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Update Invoice Status
            // Check if total recoveries match or exceed net_amount or gross_amount?
            // Usually recoveries are against the Gross? Or Net? 
            // Diagram says "Recovery Entry -> Against -> Purchase Invoice".
            // Let's assume recovery of the Gross Amount.
            $totalRecovered = $invoice->recoveries()->sum('amount');
            if ($totalRecovered >= $invoice->gross_amount) {
                $invoice->update(['status' => 'recovered']);
            } else {
                $invoice->update(['status' => 'partially_recovered']);
            }

            return redirect()->route('recoveries.index')->with('success', 'Recovery recorded and Net Profit transferred successfully.');
        });
    }

    public function destroy(Recovery $recovery)
    {
        DB::transaction(function () use ($recovery) {
            $invoice = $recovery->invoice;
            $recovery->delete();
            
            // Recalculate invoice status
            $totalRecovered = $invoice->recoveries()->sum('amount');
            if ($totalRecovered == 0) {
                $invoice->update(['status' => 'pending']);
            } elseif ($totalRecovered < $invoice->gross_amount) {
                $invoice->update(['status' => 'partially_recovered']);
            }
        });

        return redirect()->route('recoveries.index')->with('success', 'Recovery removed.');
    }
}
