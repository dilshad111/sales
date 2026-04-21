<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentParty;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'bill', 'paymentParty', 'settlements.bill']);

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('payment_party_id') && $request->payment_party_id) {
            $query->where('payment_party_id', $request->payment_party_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 20);
        $payments = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $customers = Customer::all();
        $paymentParties = PaymentParty::all();

        return view('payments.index', compact('payments', 'customers', 'paymentParties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $paymentParties = PaymentParty::where('status', 'active')->get();
        
        return view('payments.create', compact('customers', 'paymentParties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_party_id' => 'required|exists:payment_parties,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'mode' => 'required|in:cash,bank,upi,other',
            'bill_payments' => 'required|array',
            'bill_payments.*' => 'nullable|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);


        $billPayments = collect($request->bill_payments ?? [])
            ->mapWithKeys(function ($amount, $billId) {
                $normalizedAmount = is_null($amount) || $amount === '' ? null : (float) $amount;

                return [(int) $billId => $normalizedAmount];
            })
            ->filter(fn ($amount) => !is_null($amount) && $amount > 0);

        if ($billPayments->isEmpty()) {
            return back()
                ->withErrors(['bill_payments' => 'Select at least one bill and enter the payment amount.'])
                ->withInput();
        }

        $bills = Bill::with('payments')
            ->whereIn('id', $billPayments->keys())
            ->get()
            ->keyBy('id');

        $errors = [];

        $customer = \App\Models\Customer::findOrFail($request->customer_id);

        foreach ($billPayments as $billId => $amount) {
            if ($billId == 0) {
                // Opening Balance validation
                $obPaid = \App\Models\PaymentSettlement::whereHas('payment', function($q) use ($request) {
                    $q->where('customer_id', $request->customer_id);
                })->whereNull('bill_id')->sum('amount');
                
                $obOutstanding = $customer->opening_balance - $obPaid;
                
                if ($obOutstanding <= 0) {
                    $errors[] = "Opening Balance has no outstanding balance.";
                } elseif ($amount - $obOutstanding > 0.01) {
                    $errors[] = "Payment amount for Opening Balance exceeds the outstanding balance.";
                }
                continue;
            }

            $bill = $bills->get($billId);

            if (!$bill) {
                $errors[] = 'One or more selected bills are invalid.';
                continue;
            }

            if ($bill->customer_id != $request->customer_id) {
                $errors[] = "Bill {$bill->bill_number} does not belong to the selected customer.";
                continue;
            }

            $paid = $bill->payments->sum('amount');
            $outstanding = $bill->total - $paid;

            if ($outstanding <= 0) {
                $errors[] = "Bill {$bill->bill_number} has no outstanding balance.";
                continue;
            }

            if ($amount - $outstanding > 0.01) {
                $errors[] = "Payment amount for bill {$bill->bill_number} exceeds the outstanding balance.";
            }
        }

        if ($errors) {
            return back()->withErrors(['bill_payments' => implode(' ', $errors)])->withInput();
        }

        DB::transaction(function () use ($billPayments, $request) {
            $payment = Payment::create([
                'customer_id' => $request->customer_id,
                'payment_party_id' => $request->payment_party_id,
                'amount' => $billPayments->sum(),
                'payment_date' => $request->payment_date,
                'mode' => $request->mode,
                'remarks' => $request->remarks,
            ]);


            foreach ($billPayments as $billId => $amount) {
                \App\Models\PaymentSettlement::create([
                    'payment_id' => $payment->id,
                    'bill_id' => $billId ?: null,
                    'amount' => $amount,
                ]);
            }

            // Note: Ledger syncing is handled automatically by PaymentObserver@created
        });

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load('customer', 'paymentParty', 'settlements.bill');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $payment->load('customer', 'settlements.bill');
        $paymentParties = PaymentParty::where('status', 'active')->get();
        return view('payments.edit', compact('payment', 'paymentParties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_party_id' => 'nullable|exists:payment_parties,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'mode' => 'required|in:cash,bank,upi,other',
            'settlements' => 'required|array',
            'settlements.*' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);

        $settlementsSum = 0;
        $errors = [];

        foreach ($request->settlements as $id => $amount) {
            $settlement = \App\Models\PaymentSettlement::find($id);
            if (!$settlement || $settlement->payment_id != $payment->id) {
                $errors[] = "Invalid settlement record.";
                continue;
            }

            $bill = $settlement->bill;
            if ($bill) {
                $otherPaymentsSum = $bill->payments()->where('payment_id', '!=', $payment->id)->sum('amount');
                $maxAvailable = $bill->total - $otherPaymentsSum;
                $identifier = "bill {$bill->bill_number}";
            } else {
                // Opening Balance
                $otherPaymentsSum = \App\Models\PaymentSettlement::whereHas('payment', function($q) use ($payment) {
                    $q->where('customer_id', $payment->customer_id);
                })->whereNull('bill_id')->where('payment_id', '!=', $payment->id)->sum('amount');
                $maxAvailable = $payment->customer->opening_balance - $otherPaymentsSum;
                $identifier = "Opening Balance";
            }

            if ($amount - $maxAvailable > 0.01) {
                $errors[] = "Amount for {$identifier} exceeds outstanding balance (Max: " . number_format($maxAvailable, 2) . ")";
            }
            $settlementsSum += $amount;
        }

        if ($errors) {
            return back()->withErrors(['settlements' => implode(' ', $errors)])->withInput();
        }

        DB::transaction(function () use ($payment, $request, $settlementsSum) {
            $payment->update([
                'payment_party_id' => $request->payment_party_id,
                'payment_date' => $request->payment_date,
                'mode' => $request->mode,
                'amount' => $settlementsSum,
                'remarks' => $request->remarks,
            ]);

            foreach ($request->settlements as $id => $amount) {
                \App\Models\PaymentSettlement::where('id', $id)->update(['amount' => $amount]);
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    /**
     * Get outstanding bills for a customer (AJAX)
     */
    public function getOutstandingBills(Request $request)
    {
        $customerId = $request->customer_id;
        $customer = \App\Models\Customer::findOrFail($customerId);
        
        $bills = Bill::where('customer_id', $customerId)
            ->with(['billItems', 'payments'])
            ->orderBy('bill_date', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($bill) {
                $paid = $bill->payments->sum('amount');
                $outstanding = $bill->total - $paid;
                if ($outstanding > 0.01) {
                    return [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'bill_date' => $bill->bill_date->format('Y-m-d'),
                        'total' => $bill->total,
                        'paid' => $paid,
                        'outstanding' => $outstanding,
                    ];
                }
                return null;
            })
            ->filter()
            ->values();

        // Calculate Opening Balance Remaining
        if ($customer->opening_balance > 0) {
            $obPaid = \App\Models\PaymentSettlement::whereHas('payment', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })->whereNull('bill_id')->sum('amount');
            
            $obOutstanding = $customer->opening_balance - $obPaid;
            
            if ($obOutstanding > 0.01) {
                $obRow = [
                    'id' => 0, // Special ID for Opening Balance
                    'bill_number' => 'O/B',
                    'bill_date' => '-',
                    'total' => $customer->opening_balance,
                    'paid' => $obPaid,
                    'outstanding' => $obOutstanding,
                ];
                // Insert at the beginning (FIFO)
                $bills->prepend($obRow);
            }
        }

        return response()->json($bills);
    }

    /**
     * Print the specified resource.
     */
    public function print(Payment $payment)
    {
        $payment->load('customer', 'paymentParty', 'settlements.bill');
        return view('payments.print', compact('payment'));
    }
}
