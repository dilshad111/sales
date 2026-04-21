<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentParty;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Agent;
use App\Models\PurchaseInvoice;
use App\Models\Recovery;
use App\Models\Supplier;
use App\Models\TransactionEntry;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function outstandingPayments(Request $request)
    {
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $selectedYearId = $request->input('financial_year_id');

        if ($selectedYearId) {
            $year = \App\Models\FinancialYear::find($selectedYearId);
            if ($year) {
                $request->merge(['start_date' => $year->start_date->toDateString(), 'end_date' => $year->end_date->toDateString()]);
            }
        }

        $bills = $this->getOutstandingData($request);
        $customers = Customer::all();
        
        $summary = [
            'total_billed' => $bills->sum(fn($b) => $b['bill']->total),
            'total_paid' => $bills->sum('paid'),
            'total_outstanding' => $bills->sum('outstanding'),
        ];

        $agingSummary = [
            '1-30' => $bills->where('aging_bucket', '1-30')->sum('outstanding'),
            '31-60' => $bills->where('aging_bucket', '31-60')->sum('outstanding'),
            '61-90' => $bills->where('aging_bucket', '61-90')->sum('outstanding'),
            '91+' => $bills->where('aging_bucket', '91+')->sum('outstanding'),
        ];

        return view('reports.outstanding_payments', compact('bills', 'customers', 'summary', 'agingSummary', 'financialYears', 'selectedYearId'));
    }

    public function sales(Request $request)
    {
        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $selectedYearId = $request->input('financial_year_id');

        if ($selectedYearId) {
            $year = \App\Models\FinancialYear::find($selectedYearId);
            if ($year) {
                $request->merge(['start_date' => $year->start_date->toDateString(), 'end_date' => $year->end_date->toDateString()]);
            }
        }

        $query = Bill::with('customer', 'billItems.item', 'payments');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }

        $bills = $query->get();

        $itemSales = [];
        $totalSales = 0;
        $totalPayments = 0;
        $totalOutstanding = 0;

        foreach ($bills as $bill) {
            $totalSales += $bill->total;
            $paid = $bill->payments->sum('amount');
            $totalPayments += $paid;
            $totalOutstanding += $bill->total - $paid;

            foreach ($bill->billItems as $billItem) {
                $itemId = $billItem->item_id;
                if (!isset($itemSales[$itemId])) {
                    $itemSales[$itemId] = [
                        'item' => $billItem->item,
                        'quantity' => 0,
                        'total' => 0,
                    ];
                }
                $itemSales[$itemId]['quantity'] += $billItem->quantity;
                $itemSales[$itemId]['total'] += $billItem->total;
            }
        }

        // Add Opening Balance to totals to match Outstanding Payments Report
        $this->calculateOpeningBalanceStats($request->customer_id, $totalSales, $totalPayments, $totalOutstanding);

        $customers = Customer::all();

        return view('reports.sales', compact('bills', 'itemSales', 'customers', 'totalSales', 'totalPayments', 'totalOutstanding', 'financialYears', 'selectedYearId'));
    }

    public function outstandingPaymentsPdf(Request $request)
    {
        $bills = $this->getOutstandingData($request);

        $summary = [
            'total_billed' => $bills->sum(fn($b) => $b['bill']->total),
            'total_paid' => $bills->sum('paid'),
            'total_outstanding' => $bills->sum('outstanding'),
        ];

        $pdf = Pdf::loadView('reports.outstanding_payments_pdf', compact('bills', 'summary'));

        if ($request->has('print')) {
            return $pdf->stream('outstanding_payments.pdf');
        }
        return $pdf->download('outstanding_payments.pdf');
    }

    public function salesPdf(Request $request)
    {
        $query = Bill::with('customer', 'billItems.item', 'payments');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }

        $bills = $query->get();

        $itemSales = [];
        $totalSales = 0;
        $totalPayments = 0;
        $totalOutstanding = 0;

        foreach ($bills as $bill) {
            $totalSales += $bill->total;
            $paid = $bill->payments->sum('amount');
            $totalPayments += $paid;
            $totalOutstanding += $bill->total - $paid;

            foreach ($bill->billItems as $billItem) {
                $itemId = $billItem->item_id;
                if (!isset($itemSales[$itemId])) {
                    $itemSales[$itemId] = [
                        'item' => $billItem->item,
                        'quantity' => 0,
                        'total' => 0,
                    ];
                }
                $itemSales[$itemId]['quantity'] += $billItem->quantity;
                $itemSales[$itemId]['total'] += $billItem->total;
            }
        }

        // Add Opening Balance to totals
        $this->calculateOpeningBalanceStats($request->customer_id, $totalSales, $totalPayments, $totalOutstanding);

        $customers = Customer::all();

        $pdf = Pdf::loadView('reports.sales_pdf', compact('bills', 'itemSales', 'customers', 'totalSales', 'totalPayments', 'totalOutstanding'));

        if ($request->has('print')) {
            return $pdf->stream('sales_report.pdf');
        }
        return $pdf->download('sales_report.pdf');
    }

    public function customerStatement(Request $request)
    {
        $customers = Customer::all();
        $res = $this->buildCustomerStatementData($request);
        $customer = $res['customer'];
        $transactions = $res['transactions'];
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return view('reports.customer_statement', compact('customers', 'transactions', 'customer', 'startDate', 'endDate'));
    }

    public function customerStatementPdf(Request $request)
    {
        $res = $this->buildCustomerStatementData($request);
        $customer = $res['customer'];
        $transactions = $res['transactions'];
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $pdf = Pdf::loadView('reports.customer_statement_pdf', compact('customer', 'transactions', 'startDate', 'endDate'));
        
        if ($request->has('print')) {
            return $pdf->stream('customer_statement_' . ($customer ? $customer->name : 'all') . '.pdf');
        }
        return $pdf->download('customer_statement_' . ($customer ? $customer->name : 'all') . '.pdf');
    }

    public function cashStatement(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $paymentsQuery = Payment::with(['customer', 'paymentParty', 'bill']);

        if ($startDate) $paymentsQuery->where('payment_date', '>=', $startDate);
        if ($endDate) $paymentsQuery->where('payment_date', '<=', $endDate);

        $payments = $paymentsQuery->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->get();
        $customers = Customer::where('type', 'Un-Official')->get();
        $reports = [];
        
        foreach ($customers as $customer) {
            $customerPayments = $payments->where('customer_id', $customer->id);
            $totalSales = Bill::where('customer_id', $customer->id)->sum('total');
            $totalPaid = Payment::where('customer_id', $customer->id)->sum('amount');
            $outstanding = ($customer->opening_balance ?? 0) + $totalSales - $totalPaid;

            $reports[] = [
                'customer' => $customer,
                'payments' => $customerPayments,
                'subtotal' => $customerPayments->sum('amount'),
                'outstanding' => $outstanding
            ];
        }

        $totalReceived = $payments->sum('amount');
        $allBillsTotal = Bill::sum('total');
        $allPaymentsTotal = Payment::sum('amount');
        $allOBTotal = Customer::sum('opening_balance');
        $totalReceivable = $allOBTotal + $allBillsTotal - $allPaymentsTotal;

        $paymentParties = PaymentParty::all();
        $partySummary = [];
        foreach ($paymentParties as $party) {
            $partyAmount = $payments->where('payment_party_id', $party->id)->sum('amount');
            $partySummary[] = ['name' => $party->name, 'amount' => $partyAmount > 0 ? $partyAmount : 0];
        }

        return view('reports.cash_statement', compact('reports', 'totalReceived', 'totalReceivable', 'partySummary', 'startDate', 'endDate'));
    }

    public function cashStatementPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $paymentsQuery = Payment::with(['customer', 'paymentParty', 'bill']);

        if ($startDate) $paymentsQuery->where('payment_date', '>=', $startDate);
        if ($endDate) $paymentsQuery->where('payment_date', '<=', $endDate);

        $payments = $paymentsQuery->orderBy('payment_date', 'desc')->orderBy('id', 'desc')->get();
        $customers = Customer::where('type', 'Un-Official')->get();
        $reports = [];
        foreach ($customers as $customer) {
            $customerPayments = $payments->where('customer_id', $customer->id);
            $totalSales = Bill::where('customer_id', $customer->id)->sum('total');
            $totalPaid = Payment::where('customer_id', $customer->id)->sum('amount');
            $outstanding = ($customer->opening_balance ?? 0) + $totalSales - $totalPaid;

            $reports[] = [
                'customer' => $customer,
                'payments' => $customerPayments,
                'subtotal' => $customerPayments->sum('amount'),
                'outstanding' => $outstanding
            ];
        }

        $totalReceived = $payments->sum('amount');
        $allBillsTotal = Bill::sum('total');
        $allPaymentsTotal = Payment::sum('amount');
        $allOBTotal = Customer::sum('opening_balance');
        $totalReceivable = $allOBTotal + $allBillsTotal - $allPaymentsTotal;

        $paymentParties = PaymentParty::all();
        $partySummary = [];
        foreach ($paymentParties as $party) {
            $partyAmount = $payments->where('payment_party_id', $party->id)->sum('amount');
            $partySummary[] = ['name' => $party->name, 'amount' => $partyAmount > 0 ? $partyAmount : 0];
        }

        $pdf = Pdf::loadView('reports.cash_statement_pdf', compact('reports', 'totalReceived', 'totalReceivable', 'partySummary', 'startDate', 'endDate'));
        
        if ($request->has('print')) {
            return $pdf->stream('cash_statement.pdf');
        }
        return $pdf->download('cash_statement.pdf');
    }

    public function supplierStatement(Request $request)
    {
        $suppliers = Supplier::all();
        $res = $this->buildSupplierStatementData($request);
        $supplier = $res['supplier'];
        $transactions = $res['transactions'];
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return view('reports.supplier_statement', compact('suppliers', 'transactions', 'supplier', 'startDate', 'endDate'));
    }

    private function buildSupplierStatementData(Request $request)
    {
        $supplier = null;
        $transactions = collect();
        if ($request->supplier_id) {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $account = Account::where('supplier_id', $supplier->id)->first();
            
            if ($account) {
                $query = TransactionEntry::with(['transaction.entries.account'])
                    ->where('account_id', $account->id);

                if ($request->start_date) {
                    $query->whereHas('transaction', function($q) use ($request) {
                        $q->where('date', '>=', $request->start_date);
                    });
                }
                if ($request->end_date) {
                    $query->whereHas('transaction', function($q) use ($request) {
                        $q->where('date', '<=', $request->end_date);
                    });
                }

                $entries = $query->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
                    ->orderBy('transactions.date', 'asc')
                    ->orderBy('transactions.id', 'asc')
                    ->select('transaction_entries.*')
                    ->get();
                
                $openingBalance = (float) ($account->opening_balance ?? 0);
                
                if ($request->start_date) {
                    $preEntries = TransactionEntry::where('account_id', $account->id)
                        ->whereHas('transaction', function($q) use ($request) {
                            $q->where('date', '<', $request->start_date);
                        })
                        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                        ->first();
                    $openingBalance += ($preEntries->total_debit ?? 0) - ($preEntries->total_credit ?? 0);
                }

                $balance = $openingBalance;
                $transactions->push([
                    'date' => $request->start_date ?: ($supplier->created_at ?? now()),
                    'type' => 'opening_balance',
                    'reference' => '-',
                    'description' => 'Opening Balance',
                    'debit' => 0,
                    'credit' => 0,
                    'balance' => $balance
                ]);

                foreach ($entries as $entry) {
                    $balance += ($entry->debit - $entry->credit);
                    $transactions->push([
                        'date' => $entry->transaction->date,
                        'type' => $entry->transaction->type,
                        'reference' => $entry->transaction->transaction_number ?? '-',
                        'description' => $entry->transaction->narration,
                        'debit' => $entry->debit,
                        'credit' => $entry->credit,
                        'balance' => $balance
                    ]);
                }
            }
        }
        return ['supplier' => $supplier, 'transactions' => $transactions];
    }



    public function customerStatementExcel(Request $request)
    {
        $data = $this->buildCustomerStatementData($request);
        if (!$data['customer']) return back()->with('error', 'Please select a customer.');

        $headers = ['Date', 'Bill No.', 'Description', 'Qty', 'Rate', 'Sales Amt', 'Payment Rec', 'Balance'];
        
        return response()->streamDownload(function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data['transactions'] as $tx) {
                fputcsv($file, [
                    \Carbon\Carbon::parse($tx['date'])->format('d/m/Y'),
                    $tx['bill_no'],
                    $tx['description'],
                    $tx['quantity'],
                    $tx['rate'],
                    $tx['sales_amount'],
                    $tx['payment_received'],
                    $tx['balance']
                ]);
            }
            fclose($file);
        }, 'customer_statement_' . ($data['customer']->name) . '_' . now()->format('Ymd') . '.csv');
    }

    public function cashStatementExcel(Request $request)
    {
        // Logic from cashStatement
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $paymentsQuery = Payment::with(['customer', 'paymentParty', 'bill']);
        if ($startDate) $paymentsQuery->where('payment_date', '>=', $startDate);
        if ($endDate) $paymentsQuery->where('payment_date', '<=', $endDate);
        $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();
        
        $headers = ['Date', 'Customer', 'Bill #', 'Payment Party', 'Amount', 'Remarks'];
        
        return response()->streamDownload(function() use ($payments, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->payment_date->format('d/m/Y'),
                    $p->customer->name,
                    $p->bill ? $p->bill->bill_number : '-',
                    $p->paymentParty ? $p->paymentParty->name : '-',
                    $p->amount,
                    $p->remarks
                ]);
            }
            fclose($file);
        }, 'cash_statement_' . now()->format('Ymd') . '.csv');
    }

    private function buildCustomerStatementData(Request $request)
    {
        $customer = null;
        $transactions = collect();
        if ($request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
            $billItems = BillItem::with(['bill', 'item'])->whereHas('bill', function($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
                if ($request->start_date) $q->where('bill_date', '>=', $request->start_date);
                if ($request->end_date) $q->where('bill_date', '<=', $request->end_date);
            })->get();

            $billExpenses = \App\Models\BillExpense::whereHas('bill', function($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
                if ($request->start_date) $q->where('bill_date', '>=', $request->start_date);
                if ($request->end_date) $q->where('bill_date', '<=', $request->end_date);
            })->with('bill')->get();
            
            $payments = Payment::with('bill')->where('customer_id', $request->customer_id)->where(function($q) use ($request) {
                if ($request->start_date) $q->where('payment_date', '>=', $request->start_date);
                if ($request->end_date) $q->where('payment_date', '<=', $request->end_date);
            })->get();

            $openingBalance = (float) $customer->opening_balance;
            if ($request->start_date) {
                $preSales = Bill::where('customer_id', $request->customer_id)->where('bill_date', '<', $request->start_date)->sum('total');
                $prePayments = Payment::where('customer_id', $request->customer_id)->where('payment_date', '<', $request->start_date)->sum('amount');
                $openingBalance += $preSales - $prePayments;
            }

            foreach ($billItems as $bi) $transactions->push(['type' => 'sale', 'date' => $bi->delivery_date ?? $bi->bill->bill_date, 'bill_no' => $bi->bill->bill_number, 'description' => $bi->item->name, 'quantity' => $bi->quantity, 'rate' => $bi->price, 'sales_amount' => $bi->total, 'payment_received' => 0]);
            foreach ($billExpenses as $be) $transactions->push(['type' => 'sale', 'date' => $be->bill->bill_date, 'bill_no' => $be->bill->bill_number, 'description' => $be->description ?? 'Bill Expense', 'quantity' => '-', 'rate' => '-', 'sales_amount' => $be->amount, 'payment_received' => 0]);
            foreach ($payments as $p) $transactions->push(['type' => 'payment', 'date' => $p->payment_date, 'bill_no' => $p->bill ? $p->bill->bill_number : 'Multiple', 'description' => 'Payment' . ($p->remarks ? ' - ' . $p->remarks : ''), 'quantity' => '-', 'rate' => '-', 'sales_amount' => 0, 'payment_received' => $p->amount]);

            $transactions = $transactions->sortBy('date')->values();
            $transactions->prepend(['type' => 'opening_balance', 'date' => $request->start_date ? \Carbon\Carbon::parse($request->start_date) : ($customer->created_at ?? now()), 'bill_no' => '-', 'description' => 'Opening Balance', 'quantity' => '-', 'rate' => '-', 'sales_amount' => 0, 'payment_received' => 0, 'balance' => $openingBalance]);

            $balance = $openingBalance;
            $transactions = $transactions->map(function($tx, $key) use (&$balance) {
                if ($key === 0) return $tx; $balance += $tx['sales_amount'] - $tx['payment_received']; $tx['balance'] = $balance; return $tx;
            });
        }
        return ['customer' => $customer, 'transactions' => $transactions];
    }

    public function salesExcel(Request $request)
    {
        $query = Bill::with('customer', 'payments');
        if ($request->customer_id) $query->where('customer_id', $request->customer_id);
        if ($request->start_date) $query->where('bill_date', '>=', $request->start_date);
        if ($request->end_date) $query->where('bill_date', '<=', $request->end_date);
        
        $bills = $query->get();
        $headers = ['Bill #', 'Customer', 'Date', 'Total', 'Paid', 'Outstanding'];
        
        return response()->streamDownload(function() use ($bills, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($bills as $bill) {
                fputcsv($file, [
                    $bill->bill_number,
                    $bill->customer->name,
                    $bill->bill_date->format('d/m/Y'),
                    $bill->total,
                    $bill->payments->sum('amount'),
                    $bill->total - $bill->payments->sum('amount')
                ]);
            }
            fclose($file);
        }, 'sales_report_' . now()->format('Ymd') . '.csv');
    }

    public function outstandingPaymentsExcel(Request $request)
    {
        // Reuse logic from outstandingPayments
        $bills = $this->getOutstandingData($request);
        
        $headers = ['Bill #', 'Customer', 'Date', 'Total', 'Paid', 'Outstanding', 'Status'];
        
        return response()->streamDownload(function() use ($bills, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($bills as $data) {
                fputcsv($file, [
                    $data['bill']->bill_number,
                    $data['bill']->customer->name,
                    $data['bill']->bill_date->format('d/m/Y'),
                    $data['bill']->total,
                    $data['paid'],
                    $data['outstanding'],
                    ucfirst(str_replace('_', ' ', $data['status']))
                ]);
            }
            fclose($file);
        }, 'outstanding_payments_' . now()->format('Ymd') . '.csv');
    }

    private function getOutstandingData(Request $request)
    {
        $finalData = collect();
        $customersQuery = Customer::query();
        if ($request->customer_id) $customersQuery->where('id', $request->customer_id);
        
        foreach ($customersQuery->get() as $customer) {
            if ($customer->opening_balance > 0) {
                $obPaid = \App\Models\PaymentSettlement::whereNull('bill_id')
                    ->whereHas('payment', fn($q) => $q->where('customer_id', $customer->id))
                    ->sum('amount');
                $obOutstanding = $customer->opening_balance - $obPaid;
                if ($obOutstanding > 0.01) {
                    $status = $obPaid == 0 ? 'outstanding' : 'partially_paid';
                    if ($request->has('status') && $request->status != '' && $request->status != $status) {
                        if (!($request->status == 'outstanding' && $status == 'partially_paid')) continue;
                    }

                    $billDate = $customer->created_at ?? now();
                    $days = now()->diffInDays($billDate);
                    $bucket = $days <= 30 ? '1-30' : ($days <= 60 ? '31-60' : ($days <= 90 ? '61-90' : '91+'));

                    $finalData->push([
                        'bill' => (object)['bill_number' => 'O/B', 'bill_date' => $billDate, 'customer' => $customer, 'total' => $customer->opening_balance],
                        'paid' => $obPaid, 
                        'outstanding' => $obOutstanding, 
                        'status' => $status,
                        'days_overdue' => $days,
                        'aging_bucket' => $bucket
                    ]);
                }
            }
        }

        $query = Bill::with('customer', 'payments');
        if ($request->customer_id) $query->where('customer_id', $request->customer_id);
        if ($request->start_date) $query->where('bill_date', '>=', $request->start_date);
        if ($request->end_date) $query->where('bill_date', '<=', $request->end_date);
        
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'paid') $query->whereRaw('(SELECT SUM(amount) FROM payment_settlements WHERE payment_settlements.bill_id = bills.id) >= bills.total');
            elseif ($request->status == 'partially_paid') $query->whereRaw('(SELECT SUM(amount) FROM payment_settlements WHERE payment_settlements.bill_id = bills.id) > 0 AND (SELECT SUM(amount) FROM payment_settlements WHERE payment_settlements.bill_id = bills.id) < bills.total');
            elseif ($request->status == 'outstanding') $query->whereRaw('(SELECT SUM(amount) FROM payment_settlements WHERE payment_settlements.bill_id = bills.id) < bills.total OR (SELECT SUM(amount) FROM payment_settlements WHERE payment_settlements.bill_id = bills.id) IS NULL');
        }

        $billsData = $query->get()->map(function ($bill) {
            $paid = $bill->payments->sum('amount');
            $outstanding = $bill->total - $paid;
            $status = $paid == 0 ? 'outstanding' : ($paid >= $bill->total ? 'paid' : 'partially_paid');
            
            $days = now()->diffInDays($bill->bill_date);
            $bucket = $days <= 30 ? '1-30' : ($days <= 60 ? '31-60' : ($days <= 90 ? '61-90' : '91+'));

            return [
                'bill' => $bill, 
                'paid' => $paid, 
                'outstanding' => $outstanding, 
                'status' => $status,
                'days_overdue' => $days,
                'aging_bucket' => $bucket
            ];
        });

        return $finalData->concat($billsData);
    }

    public function purchaseReport(Request $request)
    {
        $query = PurchaseInvoice::with(['supplier', 'agent']);
        
        if ($request->supplier_id) $query->where('supplier_id', $request->supplier_id);
        if ($request->agent_id) $query->where('agent_id', $request->agent_id);
        if ($request->start_date) $query->where('date', '>=', $request->start_date);
        if ($request->end_date) $query->where('date', '<=', $request->end_date);

        $invoices = $query->orderBy('date', 'desc')->get();
        $suppliers = Supplier::all();
        $agents = Agent::all();

        return view('reports.purchase_report', compact('invoices', 'suppliers', 'agents'));
    }

    public function agentCommissionReport(Request $request)
    {
        $query = PurchaseInvoice::with('agent');

        if ($request->agent_id) $query->where('agent_id', $request->agent_id);
        if ($request->start_date) $query->where('date', '>=', $request->start_date);
        if ($request->end_date) $query->where('date', '<=', $request->end_date);

        $data = $query->get()->groupBy('agent_id')->map(function($invoices) {
            return [
                'agent' => $invoices->first()->agent,
                'total_purchases' => $invoices->sum('gross_amount'),
                'total_commission' => $invoices->sum('commission_amount'),
                'invoice_count' => $invoices->count(),
            ];
        });

        $agents = Agent::all();
        return view('reports.agent_commission_report', compact('data', 'agents'));
    }

    public function directorPayoutReport(Request $request)
    {
        $query = Recovery::with('directorAccount');

        if ($request->start_date) $query->where('date', '>=', $request->start_date);
        if ($request->end_date) $query->where('date', '<=', $request->end_date);

        $recoveries = $query->orderBy('date', 'desc')->get();
        
        $summary = $recoveries->groupBy('director_account_id')->map(function($group) {
            return [
                'account' => $group->first()->directorAccount,
                'total' => $group->sum('net_amount_transfered')
            ];
        });

        return view('reports.director_payout_report', compact('recoveries', 'summary'));
    }

    private function calculateOpeningBalanceStats($customerId, &$totalSales, &$totalPayments, &$totalOutstanding)
    {
        $customersQuery = Customer::query();
        if ($customerId) {
            $customersQuery->where('id', $customerId);
        }

        foreach ($customersQuery->get() as $customer) {
            if ($customer->opening_balance > 0) {
                $obPaid = \App\Models\PaymentSettlement::whereNull('bill_id')
                    ->whereHas('payment', fn($q) => $q->where('customer_id', $customer->id))
                    ->sum('amount');
                
                $totalSales += $customer->opening_balance;
                $totalPayments += $obPaid;
                $totalOutstanding += ($customer->opening_balance - $obPaid);
            }
        }
    }



    public function inventory(Request $request)
    {
        $query = Item::with('customer')
            ->withCount(['billItems as total_qty_sold' => function($q) {
                $q->select(\DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->withCount(['billItems as total_revenue' => function($q) {
                $q->select(\DB::raw('COALESCE(SUM(quantity * price), 0)'));
            }]);

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('reports.inventory', compact('items', 'customers'));
    }

    public function paymentPartiesList()
    {
        $parties = PaymentParty::with('account')->get();
        return view('reports.payment_parties_list', compact('parties'));
    }

    public function paymentPartyStatement(PaymentParty $paymentParty, Request $request)
    {
        $account = $paymentParty->account;
        if (!$account) {
            return back()->with('error', 'No ledger account found for this party.');
        }

        return redirect()->route('ledger.show', [
            'account' => $account->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    public function cashLedger(Request $request)
    {
        $account = Account::where('name', 'like', '%Cash%')->first();
        if (!$account) return back()->with('error', 'Cash Account not found.');
        return redirect()->route('ledger.show', ['account' => $account->id]);
    }

    public function bankLedger(Request $request)
    {
        $account = Account::where('name', 'like', '%Bank%')->first();
        if (!$account) return back()->with('error', 'Bank Account not found.');
        return redirect()->route('ledger.show', ['account' => $account->id]);
    }

    public function writeoffLedger(Request $request)
    {
        $account = Account::where('name', 'like', '%Write%')->first();
        if (!$account) return back()->with('error', 'Writeoff Account not found.');
        return redirect()->route('ledger.show', ['account' => $account->id]);
    }
}


