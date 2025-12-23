<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Commission;
use App\Models\CommissionDetail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalmanCommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::with(['customer', 'user', 'details'])
            ->orderBy('commission_date', 'desc')
            ->paginate(10);
            
        return view('salman_commissions.index', compact('commissions'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        // Only show users with 'Agent' role
        $users = User::where('role', 'Agent')->orderBy('name')->get();
        $salman = User::where('name', 'like', '%Salman%')->where('role', 'Agent')->first();
        
        return view('salman_commissions.create', compact('customers', 'users', 'salman'));
    }

    public function getCustomerBills(Request $request)
    {
        $customerId = $request->customer_id;
        
        $bills = Bill::where('customer_id', $customerId)
            ->with(['commissionDetails.commission'])
            ->orderBy('bill_date', 'desc')
            ->get()
            ->map(function ($bill) {
                $commission = $bill->commissionDetails->first();
                return [
                    'id' => $bill->id,
                    'bill_number' => $bill->bill_number,
                    'bill_date' => $bill->bill_date->format('d/m/Y'),
                    'total' => $bill->total,
                    'is_commissioned' => !is_null($commission),
                    'commission_id' => $commission ? $commission->commission_id : null,
                ];
            });
            
        return response()->json($bills);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'commission_date' => 'required|date',
            'bills' => 'required|array',
            'bills.*.id' => 'required|exists:bills,id',
            'bills.*.percent' => 'required|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $details = [];

            foreach ($request->bills as $billData) {
                // Check if already commissioned
                if (CommissionDetail::where('bill_id', $billData['id'])->exists()) {
                    continue; // Or return error? User said show message. 
                    // We should probably prevent this in the UI, but check here too.
                }

                $bill = Bill::find($billData['id']);
                $commissionAmt = ($bill->total * $billData['percent']) / 100;
                $totalAmount += $commissionAmt;

                $details[] = [
                    'bill_id' => $bill->id,
                    'percentage' => $billData['percent'],
                    'amount' => $commissionAmt,
                ];
            }

            if (empty($details)) {
                return back()->with('error', 'No valid bills selected or all bills are already commissioned.');
            }

            $commission = Commission::create([
                'user_id' => $request->user_id,
                'customer_id' => $request->customer_id,
                'amount' => $totalAmount,
                'commission_date' => $request->commission_date,
                'reference' => 'Salman Commission for ' . Customer::find($request->customer_id)->name,
                'notes' => $request->notes,
            ]);

            foreach ($details as $detail) {
                $detail['commission_id'] = $commission->id;
                CommissionDetail::create($detail);
            }

            return redirect()->route('salman_commissions.index')->with('success', 'Commission bill generated successfully.');
        });
    }

    public function show(Commission $commission)
    {
        $commission->load(['customer', 'user', 'details.bill.billItems.item']);
        return view('salman_commissions.show', compact('commission'));
    }

    public function downloadPdf(Commission $commission)
    {
        $commission->load(['customer', 'user', 'details.bill.billItems.item']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('salman_commissions.pdf', compact('commission'));
        return $pdf->download('commission_bill_' . $commission->id . '.pdf');
    }
    
    public function destroy(Commission $commission)
    {
        $commission->delete();
        return redirect()->route('salman_commissions.index')->with('success', 'Commission record deleted.');
    }
}
