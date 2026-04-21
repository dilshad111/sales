<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Bill;
use App\Models\Payment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'customers' => Customer::count(),
            'items' => Item::count(),
            'bills' => Bill::count(),
            'payments' => Payment::count(),
            'total_sales' => Bill::sum('total'),
            'total_payments' => Payment::sum('amount'),
            'total_items_sold' => \App\Models\BillItem::sum('quantity'),
            'outstanding' => Customer::sum('opening_balance') + Bill::sum('total') - Payment::sum('amount'),
        ];

        // Monthly Sales and Payments for the last 12 months
        $months = [];
        $salesData = [];
        $paymentsData = [];
        $itemWiseSalesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            // Bill-wise sales (legacy/reference)
            $salesData[] = Bill::whereYear('bill_date', $date->year)
                ->whereMonth('bill_date', $date->month)
                ->sum('total');

            // Item-wise sales (as requested: sum of line items)
            $itemWiseSalesData[] = \App\Models\BillItem::whereHas('bill', function($query) use ($date) {
                $query->whereYear('bill_date', $date->year)
                    ->whereMonth('bill_date', $date->month);
            })->sum('total');

            $paymentsData[] = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
        }

        // Top 5 Customers by Sales
        $topCustomers = Customer::withSum('bills', 'total')
            ->orderByDesc('bills_sum_total')
            ->take(5)
            ->get();

        $topItems = Item::withSum('billItems', 'total')
            ->orderByDesc('bill_items_sum_total')
            ->take(5)
            ->get();

        // Aging Summary calculation
        $agingSummary = ['1-30' => 0, '31-60' => 0, '61-90' => 0, '91+' => 0];
        
        // 1. Opening Balances aging
        $customers = Customer::where('opening_balance', '>', 0)->get();
        foreach ($customers as $customer) {
            $obPaid = \App\Models\PaymentSettlement::whereNull('bill_id')
                ->whereHas('payment', fn($q) => $q->where('customer_id', $customer->id))
                ->sum('amount');
            $outstanding = $customer->opening_balance - $obPaid;
            if ($outstanding > 0.01) {
                $days = now()->diffInDays($customer->created_at ?? now());
                $bucket = $days <= 30 ? '1-30' : ($days <= 60 ? '31-60' : ($days <= 90 ? '61-90' : '91+'));
                $agingSummary[$bucket] += $outstanding;
            }
        }

        // 2. Bills aging
        $bills = Bill::withSum('payments', 'amount')->get();
        foreach ($bills as $bill) {
            $outstanding = $bill->total - ($bill->payments_sum_amount ?? 0);
            if ($outstanding > 0.01) {
                $days = now()->diffInDays($bill->bill_date);
                $bucket = $days <= 30 ? '1-30' : ($days <= 60 ? '31-60' : ($days <= 90 ? '61-90' : '91+'));
                $agingSummary[$bucket] += $outstanding;
            }
        }

        return view('home', compact('stats', 'months', 'salesData', 'itemWiseSalesData', 'paymentsData', 'topCustomers', 'topItems', 'agingSummary'));
    }
}
