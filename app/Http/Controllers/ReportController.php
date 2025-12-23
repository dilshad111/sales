<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Payment;
use App\Models\PaymentParty;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function outstandingPayments(Request $request)
    {
        $query = Bill::with('customer', 'payments');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }

        if ($request->has('status')) {
            if ($request->status == 'paid') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) >= bills.total');
            } elseif ($request->status == 'partially_paid') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) > 0 AND (SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) < bills.total');
            } elseif ($request->status == 'outstanding') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) < bills.total OR (SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) IS NULL');
            }
        }

        $bills = $query->get()->map(function ($bill) {
            $paid = $bill->payments->sum('amount');
            $outstanding = $bill->total - $paid;
            $status = $paid == 0 ? 'outstanding' : ($paid >= $bill->total ? 'paid' : 'partially_paid');
            return [
                'bill' => $bill,
                'paid' => $paid,
                'outstanding' => $outstanding,
                'status' => $status,
            ];
        });

        $customers = Customer::all();
        $summary = [
            'total_billed' => $bills->sum(fn($b) => $b['bill']->total),
            'total_paid' => $bills->sum('paid'),
            'total_outstanding' => $bills->sum('outstanding'),
        ];

        return view('reports.outstanding_payments', compact('bills', 'customers', 'summary'));
    }

    public function sales(Request $request)
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

        $customers = Customer::all();

        return view('reports.sales', compact('bills', 'itemSales', 'customers', 'totalSales', 'totalPayments', 'totalOutstanding'));
    }

    public function outstandingPaymentsPdf(Request $request)
    {
        // Similar to outstandingPayments, but return PDF
        $query = Bill::with('customer', 'payments');

        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('bill_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('bill_date', '<=', $request->end_date);
        }

        if ($request->has('status')) {
            if ($request->status == 'paid') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) >= bills.total');
            } elseif ($request->status == 'partially_paid') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) > 0 AND (SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) < bills.total');
            } elseif ($request->status == 'outstanding') {
                $query->whereRaw('(SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) < bills.total OR (SELECT SUM(amount) FROM payments WHERE payments.bill_id = bills.id) IS NULL');
            }
        }

        $bills = $query->get()->map(function ($bill) {
            $paid = $bill->payments->sum('amount');
            $outstanding = $bill->total - $paid;
            $status = $paid == 0 ? 'outstanding' : ($paid >= $bill->total ? 'paid' : 'partially_paid');
            return [
                'bill' => $bill,
                'paid' => $paid,
                'outstanding' => $outstanding,
                'status' => $status,
            ];
        });

        $summary = [
            'total_billed' => $bills->sum(fn($b) => $b['bill']->total),
            'total_paid' => $bills->sum('paid'),
            'total_outstanding' => $bills->sum('outstanding'),
        ];

        $pdf = Pdf::loadView('reports.outstanding_payments_pdf', compact('bills', 'summary'));

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

        $customers = Customer::all();

        $pdf = Pdf::loadView('reports.sales_pdf', compact('bills', 'itemSales', 'customers', 'totalSales', 'totalPayments', 'totalOutstanding'));

        return $pdf->download('sales_report.pdf');
    }

    public function customerStatement(Request $request)
    {
        $customers = Customer::all();
        $transactions = collect();
        $customer = null;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($request->has('customer_id') && $request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
            
            // Get all bill items for this customer within date range
            $billItemsQuery = BillItem::with(['bill', 'item'])
                ->whereHas('bill', function($query) use ($request) {
                    $query->where('customer_id', $request->customer_id);
                    
                    if ($request->start_date) {
                        $query->where('bill_date', '>=', $request->start_date);
                    }
                    
                    if ($request->end_date) {
                        $query->where('bill_date', '<=', $request->end_date);
                    }
                });
            
            $billItems = $billItemsQuery->get();
            
            // Get all payments for this customer within date range
            $paymentsQuery = Payment::with('bill')
                ->where('customer_id', $request->customer_id);
            
            if ($request->start_date) {
                $paymentsQuery->where('payment_date', '>=', $request->start_date);
            }
            
            if ($request->end_date) {
                $paymentsQuery->where('payment_date', '<=', $request->end_date);
            }
            
            $payments = $paymentsQuery->get();
            
            // Combine bill items and payments into transactions
            foreach ($billItems as $billItem) {
                $transactions->push([
                    'type' => 'sale',
                    'date' => $billItem->delivery_date ?? $billItem->bill->bill_date,
                    'bill_no' => $billItem->bill->bill_number,
                    'description' => $billItem->item->name,
                    'quantity' => $billItem->quantity,
                    'rate' => $billItem->price,
                    'sales_amount' => $billItem->total,
                    'payment_received' => 0,
                ]);
            }
            
            foreach ($payments as $payment) {
                $transactions->push([
                    'type' => 'payment',
                    'date' => $payment->payment_date,
                    'bill_no' => $payment->bill ? $payment->bill->bill_number : 'N/A',
                    'description' => 'Payment Received' . ($payment->remarks ? ' - ' . $payment->remarks : ''),
                    'quantity' => '-',
                    'rate' => '-',
                    'sales_amount' => 0,
                    'payment_received' => $payment->amount,
                ]);
            }
            
            // Sort transactions by date
            $transactions = $transactions->sortBy('date')->values();
            
            // Calculate running balance
            $balance = 0;
            $transactions = $transactions->map(function($transaction) use (&$balance) {
                $balance += $transaction['sales_amount'] - $transaction['payment_received'];
                $transaction['balance'] = $balance;
                return $transaction;
            });
        }

        return view('reports.customer_statement', compact('customers', 'transactions', 'customer', 'startDate', 'endDate'));
    }

    public function customerStatementPdf(Request $request)
    {
        $customer = null;
        $transactions = collect();
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($request->has('customer_id') && $request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
            
            // Get all bill items for this customer within date range
            $billItemsQuery = BillItem::with(['bill', 'item'])
                ->whereHas('bill', function($query) use ($request) {
                    $query->where('customer_id', $request->customer_id);
                    
                    if ($request->start_date) {
                        $query->where('bill_date', '>=', $request->start_date);
                    }
                    
                    if ($request->end_date) {
                        $query->where('bill_date', '<=', $request->end_date);
                    }
                });
            
            $billItems = $billItemsQuery->get();
            
            // Get all payments for this customer within date range
            $paymentsQuery = Payment::with('bill')
                ->where('customer_id', $request->customer_id);
            
            if ($request->start_date) {
                $paymentsQuery->where('payment_date', '>=', $request->start_date);
            }
            
            if ($request->end_date) {
                $paymentsQuery->where('payment_date', '<=', $request->end_date);
            }
            
            $payments = $paymentsQuery->get();
            
            // Combine bill items and payments into transactions
            foreach ($billItems as $billItem) {
                $transactions->push([
                    'type' => 'sale',
                    'date' => $billItem->delivery_date ?? $billItem->bill->bill_date,
                    'bill_no' => $billItem->bill->bill_number,
                    'description' => $billItem->item->name,
                    'quantity' => $billItem->quantity,
                    'rate' => $billItem->price,
                    'sales_amount' => $billItem->total,
                    'payment_received' => 0,
                ]);
            }
            
            foreach ($payments as $payment) {
                $transactions->push([
                    'type' => 'payment',
                    'date' => $payment->payment_date,
                    'bill_no' => $payment->bill ? $payment->bill->bill_number : 'N/A',
                    'description' => 'Payment Received' . ($payment->remarks ? ' - ' . $payment->remarks : ''),
                    'quantity' => '-',
                    'rate' => '-',
                    'sales_amount' => 0,
                    'payment_received' => $payment->amount,
                ]);
            }
            
            // Sort transactions by date
            $transactions = $transactions->sortBy('date')->values();
            
            // Calculate running balance
            $balance = 0;
            $transactions = $transactions->map(function($transaction) use (&$balance) {
                $balance += $transaction['sales_amount'] - $transaction['payment_received'];
                $transaction['balance'] = $balance;
                return $transaction;
            });
        }

        $pdf = Pdf::loadView('reports.customer_statement_pdf', compact('customer', 'transactions', 'startDate', 'endDate'));
        
        return $pdf->download('customer_statement_' . ($customer ? $customer->name : 'all') . '.pdf');
    }

    public function cashStatement(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $paymentsQuery = Payment::with(['customer', 'paymentParty', 'bill']);

        if ($startDate) {
            $paymentsQuery->where('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $paymentsQuery->where('payment_date', '<=', $endDate);
        }

        $payments = $paymentsQuery->get();

        $customers = Customer::where('type', 'Un-Official')->get();
        $reports = [];
        
        foreach ($customers as $customer) {
            $customerPayments = $payments->where('customer_id', $customer->id);
            
            // Calculate outstanding for this customer
            $totalSales = Bill::where('customer_id', $customer->id)->sum('total');
            $totalPaid = Payment::where('customer_id', $customer->id)->sum('amount');
            $outstanding = $totalSales - $totalPaid;

            // Only skip OR keep? User says "show all other customer... mandatory"
            // So we show everyone.
            $reports[] = [
                'customer' => $customer,
                'payments' => $customerPayments,
                'subtotal' => $customerPayments->sum('amount'),
                'outstanding' => $outstanding
            ];
        }

        // Summary calculations
        $totalReceived = $payments->sum('amount');
        
        // Total receivable (Total Outstanding of all customers)
        $allBillsTotal = Bill::sum('total');
        $allPaymentsTotal = Payment::sum('amount');
        $totalReceivable = $allBillsTotal - $allPaymentsTotal;

        // Payment Party wise summary
        $paymentParties = PaymentParty::all();
        $partySummary = [];
        foreach ($paymentParties as $party) {
            $partyAmount = $payments->where('payment_party_id', $party->id)->sum('amount');
            $partySummary[] = [
                'name' => $party->name,
                'amount' => $partyAmount > 0 ? $partyAmount : 0
            ];
        }

        return view('reports.cash_statement', compact('reports', 'totalReceived', 'totalReceivable', 'partySummary', 'startDate', 'endDate'));
    }

    public function cashStatementPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $paymentsQuery = Payment::with(['customer', 'paymentParty', 'bill']);

        if ($startDate) {
            $paymentsQuery->where('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $paymentsQuery->where('payment_date', '<=', $endDate);
        }

        $payments = $paymentsQuery->get();

        $customers = Customer::where('type', 'Un-Official')->get();
        $reports = [];
        foreach ($customers as $customer) {
            $customerPayments = $payments->where('customer_id', $customer->id);
            
            $totalSales = Bill::where('customer_id', $customer->id)->sum('total');
            $totalPaid = Payment::where('customer_id', $customer->id)->sum('amount');
            $outstanding = $totalSales - $totalPaid;

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
        $totalReceivable = $allBillsTotal - $allPaymentsTotal;

        $paymentParties = PaymentParty::all();
        $partySummary = [];
        foreach ($paymentParties as $party) {
            $partyAmount = $payments->where('payment_party_id', $party->id)->sum('amount');
            $partySummary[] = [
                'name' => $party->name,
                'amount' => $partyAmount > 0 ? $partyAmount : 0
            ];
        }

        $pdf = Pdf::loadView('reports.cash_statement_pdf', compact('reports', 'totalReceived', 'totalReceivable', 'partySummary', 'startDate', 'endDate'));
        
        return $pdf->download('cash_statement.pdf');
    }
}
