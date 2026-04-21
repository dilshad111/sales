<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['entries.account', 'created_by_user']);

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->has('financial_year_id') && $request->financial_year_id) {
            $query->where('financial_year_id', $request->financial_year_id);
        }

        $vouchers = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(20);
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();

        return view('vouchers.index', compact('vouchers', 'financialYears'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'JV');
        $accounts = Account::where('status', 'active')->where('is_group', false)->orderBy('name')->get();
        $banks = \App\Models\Bank::where('status', 'active')->orderBy('name')->get();
        
        // Suggest a default cash/bank account for PV/RV if available
        $cashAccount = Account::where('name', 'like', '%Cash%')->first();
        $bankAccount = Account::where('name', 'like', '%Bank%')->first();

        // Optional pre-filling from query params
        $prefillAccount = $request->has('account_id') ? Account::find($request->account_id) : null;
        $prefillAmount = $request->get('amount', 0);

        return view('vouchers.create', compact('type', 'accounts', 'cashAccount', 'bankAccount', 'banks', 'prefillAccount', 'prefillAmount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:PV,RV,JV',
            'date' => 'required|date',
            'narration' => 'nullable|string|max:500',
            'payment_mode' => 'required_if:type,PV,RV|in:cash,bank',
            'bank_id' => 'nullable|required_if:payment_mode,bank|exists:banks,id',
            'cheque_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100', // Still keeping for legacy or manual entry
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => [
                'required',
                'exists:accounts,id',
                function ($attribute, $value, $fail) {
                    if (Account::find($value)?->is_group) {
                        $fail('Transactions cannot be posted to group accounts.');
                    }
                },
            ],
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($request->entries)->sum('debit');
        $totalCredit = collect($request->entries)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->with('error', 'Debit and Credit totals must match.')->withInput();
        }

        if ($totalDebit <= 0) {
            return back()->with('error', 'Transaction amount must be greater than zero.')->withInput();
        }

        try {
            DB::beginTransaction();

            $transactionData = [
                'date' => $request->date,
                'type' => $request->type,
                'narration' => $request->narration,
                'payment_mode' => $request->payment_mode,
                'bank_id' => $request->bank_id,
                'cheque_number' => $request->cheque_number,
                'bank_name' => $request->bank_name,
                'total_amount' => $totalDebit,
                'created_by' => Auth::id(),
            ];

            \Log::info('Voucher Store Attempt:', $transactionData);

            $transaction = Transaction::create($transactionData);

            foreach ($request->entries as $entry) {
                if ($entry['debit'] > 0 || $entry['credit'] > 0) {
                    TransactionEntry::create([
                        'transaction_id' => $transaction->id,
                        'account_id' => $entry['account_id'],
                        'debit' => $entry['debit'],
                        'credit' => $entry['credit'],
                    ]);

                    // FIFO Settlement for Customers
                    $account = Account::find($entry['account_id']);
                    if ($entry['credit'] > 0 && $account && $account->customer_id) {
                        $this->applyFifoSettlement($account->customer_id, $entry['credit'], $transaction);
                    }
                }
            }


            DB::commit();
            \Log::info('Voucher Stored Successfully:', ['id' => $transaction->id, 'number' => $transaction->transaction_number]);
            return redirect()->route('vouchers.index')->with('success', 'Voucher ' . $transaction->transaction_number . ' created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Voucher Store Fatal Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error creating voucher: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaction $voucher)
    {
        $voucher->load(['entries.account', 'created_by_user']);
        return view('vouchers.show', compact('voucher'));
    }

    public function print(Transaction $voucher)
    {
        $voucher->load(['entries.account', 'created_by_user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vouchers.print', compact('voucher'));
        return $pdf->stream('Voucher_' . $voucher->transaction_number . '.pdf');
    }

    public function destroy(Transaction $voucher)
    {
        try {
            DB::beginTransaction();
            // Delete associated settlements if any
            $paymentIds = \App\Models\Payment::where('remarks', 'like', "%Voucher: #".$voucher->transaction_number."%")->pluck('id');
            \App\Models\PaymentSettlement::whereIn('payment_id', $paymentIds)->delete();
            \App\Models\Payment::whereIn('id', $paymentIds)->delete();

            $voucher->entries()->delete();
            $voucher->delete();
            DB::commit();
            return redirect()->route('vouchers.index')->with('success', 'Voucher deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting voucher: ' . $e->getMessage());
        }
    }

    public function edit(Transaction $voucher)
    {
        $voucher->load('entries.account');
        $type = $voucher->type;
        $accounts = Account::where('status', 'active')->where('is_group', false)->orderBy('name')->get();
        $banks = \App\Models\Bank::where('status', 'active')->orderBy('name')->get();
        
        $cashAccount = Account::where('name', 'like', '%Cash%')->first();
        $bankAccount = Account::where('name', 'like', '%Bank%')->first();

        return view('vouchers.edit', compact('voucher', 'type', 'accounts', 'cashAccount', 'bankAccount', 'banks'));
    }

    public function update(Request $request, Transaction $voucher)
    {
        $request->validate([
            'date' => 'required|date',
            'narration' => 'nullable|string|max:500',
            'payment_mode' => 'required_if:type,PV,RV|in:cash,bank',
            'bank_id' => 'nullable|required_if:payment_mode,bank|exists:banks,id',
            'cheque_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => [
                'required',
                'exists:accounts,id',
                function ($attribute, $value, $fail) {
                    if (Account::find($value)?->is_group) {
                        $fail('Transactions cannot be posted to group accounts.');
                    }
                },
            ],
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($request->entries)->sum('debit');
        $totalCredit = collect($request->entries)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->with('error', 'Debit and Credit totals must match.')->withInput();
        }

        if ($totalDebit <= 0) {
            return back()->with('error', 'Transaction amount must be greater than zero.')->withInput();
        }

        try {
            DB::beginTransaction();

            // Update main transaction
            $voucher->update([
                'date' => $request->date,
                'narration' => $request->narration,
                'payment_mode' => $request->payment_mode,
                'bank_id' => $request->bank_id,
                'cheque_number' => $request->cheque_number,
                'bank_name' => $request->bank_name,
                'total_amount' => $totalDebit,
                'updated_by' => Auth::id(),
            ]);

            // Clear old entries and settlements
            $voucher->entries()->delete();
            
            // Clean up related FIFO settlements to re-apply
            $paymentIds = \App\Models\Payment::where('remarks', 'like', "%Voucher: #".$voucher->transaction_number."%")->pluck('id');
            \App\Models\PaymentSettlement::whereIn('payment_id', $paymentIds)->delete();
            \App\Models\Payment::whereIn('id', $paymentIds)->delete();

            // Insert new entries
            foreach ($request->entries as $entry) {
                if ($entry['debit'] > 0 || $entry['credit'] > 0) {
                    TransactionEntry::create([
                        'transaction_id' => $voucher->id,
                        'account_id' => $entry['account_id'],
                        'debit' => $entry['debit'],
                        'credit' => $entry['credit'],
                    ]);

                    // Re-apply FIFO Settlement for Customers if credit
                    $account = Account::find($entry['account_id']);
                    if ($entry['credit'] > 0 && $account && $account->customer_id) {
                        $this->applyFifoSettlement($account->customer_id, $entry['credit'], $voucher);
                    }
                }
            }

            DB::commit();
            return redirect()->route('vouchers.index')->with('success', 'Voucher updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating voucher: ' . $e->getMessage())->withInput();
        }
    }

    private function applyFifoSettlement($customerId, $amount, Transaction $transaction)

    {
        $customer = \App\Models\Customer::findOrFail($customerId);
        
        // 1. Create a dummy Payment record to link settlements to (avoiding ledger events)
        $payment = \App\Models\Payment::withoutEvents(function() use ($customer, $amount, $transaction) {
            return \App\Models\Payment::create([
                'customer_id' => $customer->id,
                'payment_date' => $transaction->date,
                'amount' => $amount,
                'mode' => strtolower($transaction->payment_mode ?? 'other'),
                'remarks' => $transaction->narration . " (Voucher: #".$transaction->transaction_number.")"
            ]);
        });

        $remainingAmount = $amount;

        // 2. Handle Opening Balance first (FIFO)
        if ($customer->opening_balance > 0) {
            $obPaid = \App\Models\PaymentSettlement::whereHas('payment', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })->whereNull('bill_id')->sum('amount');
            
            $obOutstanding = $customer->opening_balance - $obPaid;
            
            if ($obOutstanding > 0.01 && $remainingAmount > 0) {
                $settleAmount = min($remainingAmount, $obOutstanding);
                \App\Models\PaymentSettlement::create([
                    'payment_id' => $payment->id,
                    'bill_id' => null,
                    'amount' => $settleAmount
                ]);
                $remainingAmount -= $settleAmount;
            }
        }

        // 3. Handle Bills by Date
        if ($remainingAmount > 0) {
            $bills = \App\Models\Bill::where('customer_id', $customerId)
                ->orderBy('bill_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($bills as $bill) {
                if ($remainingAmount <= 0) break;

                $paid = $bill->payments->sum('amount');
                $outstanding = $bill->total - $paid;

                if ($outstanding > 0.01) {
                    $settleAmount = min($remainingAmount, $outstanding);
                    \App\Models\PaymentSettlement::create([
                        'payment_id' => $payment->id,
                        'bill_id' => $bill->id,
                        'amount' => $settleAmount
                    ]);
                    $remainingAmount -= $settleAmount;
                }
            }
        }
    }
}

