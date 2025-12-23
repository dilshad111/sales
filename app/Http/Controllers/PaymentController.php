<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with('customer', 'bill', 'paymentParty');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('payment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        $payments = $query->paginate(10);
        $customers = Customer::all();

        return view('payments.index', compact('payments', 'customers'));
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
            'payment_party_id' => 'nullable|exists:payment_parties,id',
            'payment_date' => 'required|date',
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

        foreach ($billPayments as $billId => $amount) {
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
            foreach ($billPayments as $billId => $amount) {
                Payment::create([
                    'customer_id' => $request->customer_id,
                    'bill_id' => $billId,
                    'payment_party_id' => $request->payment_party_id,
                    'amount' => $amount,
                    'payment_date' => $request->payment_date,
                    'mode' => $request->mode,
                    'remarks' => $request->remarks,
                ]);
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
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
            'payment_date' => 'required|date',
            'mode' => 'required|in:cash,bank,upi,other',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Check if amount exceeds outstanding if it was increased
        $bill = $payment->bill;
        $otherPaymentsSum = $bill->payments()->where('id', '!=', $payment->id)->sum('amount');
        $newOutstanding = $bill->total - ($otherPaymentsSum + $request->amount);

        if ($newOutstanding < -0.01) {
            return back()->withErrors(['amount' => "Payment amount exceeds the bill's outstanding balance (Max: " . ($bill->total - $otherPaymentsSum) . ")"])->withInput();
        }

        $payment->update([
            'payment_party_id' => $request->payment_party_id,
            'payment_date' => $request->payment_date,
            'mode' => $request->mode,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
        ]);

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
        $bills = Bill::where('customer_id', $customerId)
            ->with('billItems', 'payments')
            ->get()
            ->map(function ($bill) {
                $paid = $bill->payments->sum('amount');
                $outstanding = $bill->total - $paid;
                if ($outstanding > 0) {
                    return [
                        'id' => $bill->id,
                        'bill_number' => $bill->bill_number,
                        'bill_date' => $bill->bill_date,
                        'total' => $bill->total,
                        'paid' => $paid,
                        'outstanding' => $outstanding,
                    ];
                }
                return null;
            })
            ->filter()
            ->values();

        return response()->json($bills);
    }
}
