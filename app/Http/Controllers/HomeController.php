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
        ];

        // Monthly Sales and Payments for the last 6 months
        $months = [];
        $salesData = [];
        $paymentsData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $salesData[] = Bill::whereYear('bill_date', $date->year)
                ->whereMonth('bill_date', $date->month)
                ->sum('total');

            $paymentsData[] = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
        }

        // Top 5 Customers by Sales
        $topCustomers = Customer::withSum('bills', 'total')
            ->orderByDesc('bills_sum_total')
            ->take(5)
            ->get();

        return view('home', compact('stats', 'months', 'salesData', 'paymentsData', 'topCustomers'));
    }
}
